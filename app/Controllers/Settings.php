<?php

namespace App\Controllers;

use App\Models\UserSettingModel;
use App\Models\AuditLogModel;
use App\Services\EncryptionService;
use App\Services\OpenAIService;
use CodeIgniter\HTTP\ResponseInterface;

class Settings extends BaseController
{
    protected UserSettingModel $settingsModel;
    protected EncryptionService $encryptionSvc;
    protected AuditLogModel $auditLog;

    public function __construct()
    {
        $this->settingsModel  = new UserSettingModel();
        $this->encryptionSvc  = new EncryptionService();
        $this->auditLog       = new AuditLogModel();
    }

    public function save(): ResponseInterface
    {
        $userId = session()->get('user_id');
        $data   = $this->request->getJSON(true) ?? $this->request->getPost();

        $toSave = [];

        // Handle OpenAI API key
        if (!empty($data['openai_api_key'])) {
            $key = trim($data['openai_api_key']);
            if (!$this->encryptionSvc->isValidOpenAIKey($key)) {
                return $this->response->setStatusCode(422)->setJSON([
                    'data'  => null,
                    'error' => 'Invalid API key format. OpenAI keys start with sk-',
                ]);
            }
            $toSave['openai_api_key'] = $this->encryptionSvc->encryptKey($key);
            $toSave['openai_model']   = $data['openai_model'] ?? 'gpt-4.1-nano';
        } elseif (isset($data['openai_model'])) {
            $toSave['openai_model'] = $data['openai_model'];
        }

        // Handle agency profile
        if (isset($data['agency_name']))  $toSave['agency_name']  = trim($data['agency_name']);
        if (isset($data['agency_email'])) $toSave['agency_email'] = trim($data['agency_email']);
        if (isset($data['agency_phone'])) $toSave['agency_phone'] = trim($data['agency_phone']);

        if (empty($toSave)) {
            return $this->response->setStatusCode(400)->setJSON([
                'data'  => null,
                'error' => 'No data provided to save.',
            ]);
        }

        $this->settingsModel->upsertForUser($userId, $toSave);

        // Build response
        $responseData = ['saved' => true];
        if (isset($toSave['openai_api_key'])) {
            $plain = trim($data['openai_api_key']);
            $responseData['masked_key'] = $this->encryptionSvc->maskKey($plain);
            $responseData['has_key']    = true;
        }
        if (isset($toSave['agency_name'])) {
            $responseData['agency_name'] = $toSave['agency_name'];
        }

        $this->auditLog->log($userId, 'settings.save', null, null, ['fields' => array_keys($toSave)]);

        return $this->response->setJSON(['data' => $responseData, 'error' => null]);
    }

    public function testKey(): ResponseInterface
    {
        $userId = session()->get('user_id');

        // Rate limit: 5 tests per hour
        $since = date('Y-m-d H:i:s', strtotime('-1 hour'));
        $count = $this->auditLog->countByUserAndAction($userId, 'settings.test-key', $since);
        if ($count >= 5) {
            return $this->response->setStatusCode(429)->setJSON([
                'data'  => null,
                'error' => 'Rate limit: max 5 key tests per hour.',
            ]);
        }

        $settings = $this->settingsModel->getByUserId($userId);
        if (empty($settings['openai_api_key'])) {
            return $this->response->setStatusCode(422)->setJSON([
                'data'  => null,
                'error' => 'No API key saved. Please save your key first.',
            ]);
        }

        $plain = $this->encryptionSvc->decryptKey($settings['openai_api_key']);
        $model = $settings['openai_model'] ?? 'gpt-4.1-nano';

        $this->auditLog->log($userId, 'settings.test-key');

        try {
            $openAI = new OpenAIService($plain, $model);
            $valid  = $openAI->validateKey();
            if ($valid) {
                return $this->response->setJSON([
                    'data'  => ['connected' => true, 'model' => $model],
                    'error' => null,
                ]);
            } else {
                return $this->response->setStatusCode(400)->setJSON([
                    'data'  => ['connected' => false],
                    'error' => 'Could not connect to OpenAI. Check your API key.',
                ]);
            }
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(400)->setJSON([
                'data'  => ['connected' => false],
                'error' => 'Connection failed: ' . $e->getMessage(),
            ]);
        }
    }
}
