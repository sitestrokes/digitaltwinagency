<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ProposalModel;
use App\Models\UserSettingModel;
use App\Models\AuditLogModel;
use App\Services\EncryptionService;
use App\Services\OpenAIService;
use App\Services\ProposalService;
use CodeIgniter\HTTP\ResponseInterface;

class ProposalController extends BaseController
{
    protected ProposalModel $model;
    protected UserSettingModel $settingsModel;
    protected AuditLogModel $auditLog;
    protected EncryptionService $encryptionSvc;
    protected ProposalService $proposalService;

    public function __construct()
    {
        $this->model           = new ProposalModel();
        $this->settingsModel   = new UserSettingModel();
        $this->auditLog        = new AuditLogModel();
        $this->encryptionSvc   = new EncryptionService();
        $this->proposalService = new ProposalService();
    }

    public function index(): ResponseInterface
    {
        $userId = session()->get('user_id');
        return $this->response->setJSON([
            'data'  => $this->model->getByUser($userId),
            'error' => null,
        ]);
    }

    public function show(int $id): ResponseInterface
    {
        $userId   = session()->get('user_id');
        $proposal = $this->model->getByUserAndId($userId, $id);
        if (!$proposal) {
            return $this->response->setStatusCode(404)->setJSON([
                'data' => null, 'error' => 'Proposal not found.',
            ]);
        }
        return $this->response->setJSON(['data' => $proposal, 'error' => null]);
    }

    public function generate(): ResponseInterface
    {
        $userId = session()->get('user_id');
        $data   = $this->request->getJSON(true) ?? [];

        // Validate required fields
        $rules = [
            'client_name' => 'required|min_length[2]',
            'niche'       => 'required',
            'tier'        => 'required|in_list[starter,growth,premium,custom]',
            'price'       => 'required|integer|greater_than[0]',
        ];
        $validation = \Config\Services::validation();
        $validation->setRules($rules);
        if (!$validation->run($data)) {
            return $this->response->setStatusCode(422)->setJSON([
                'data'  => null,
                'error' => implode(' ', $validation->getErrors()),
            ]);
        }

        $mode = $data['mode'] ?? 'template';

        if ($mode === 'ai') {
            // Check for API key
            $settings = $this->settingsModel->getByUserId($userId);
            if (empty($settings['openai_api_key'])) {
                return $this->response->setStatusCode(422)->setJSON([
                    'data'  => null,
                    'error' => 'No OpenAI API key configured. Please add your key in Settings (Tab 5).',
                ]);
            }

            // Rate limit: 10 AI proposals per day
            $todayCount = $this->model->countAiGenerationsToday($userId);
            if ($todayCount >= 10) {
                return $this->response->setStatusCode(429)->setJSON([
                    'data'  => null,
                    'error' => 'Daily limit reached: 10 AI proposals per day. Try again tomorrow.',
                ]);
            }

            $plainKey = $this->encryptionSvc->decryptKey($settings['openai_api_key']);
            $model    = $settings['openai_model'] ?? 'gpt-4.1-nano';

            // Build params
            $params = [
                'agency_name'  => $data['agency_name'] ?? session()->get('user_name') . ' Agency',
                'client_name'  => $data['client_name'],
                'contact_name' => $data['contact_name'] ?? $data['client_name'],
                'niche'        => $data['niche'],
                'tier'         => ucfirst($data['tier']),
                'price'        => $data['price'],
                'services'     => $data['services'] ?? [],
                'pain_points'  => $data['pain_points'] ?? [],
                'notes'        => $data['notes'] ?? '',
            ];

            try {
                $openAI  = new OpenAIService($plainKey, $model);
                $content = $openAI->generateProposal($params);
                $usage   = $openAI->getLastUsage();

                $this->auditLog->log($userId, 'proposal.generate', 'proposal', null, [
                    'mode'   => 'ai',
                    'model'  => $model,
                    'niche'  => $data['niche'],
                    'tokens' => $usage['total_tokens'] ?? 0,
                ]);

                return $this->response->setJSON([
                    'data' => [
                        'content'         => $content,
                        'generation_mode' => 'ai',
                        'tokens_used'     => $usage['total_tokens'] ?? 0,
                    ],
                    'error' => null,
                ]);
            } catch (\RuntimeException $e) {
                $msg = $e->getMessage();
                // Fall back to template on error and report
                $content = $this->proposalService->generateTemplate($params);
                return $this->response->setJSON([
                    'data' => [
                        'content'         => $content,
                        'generation_mode' => 'template',
                        'fallback_reason' => $msg,
                    ],
                    'error' => 'AI generation failed (' . $msg . '). Showing template version.',
                ]);
            }
        }

        // Template mode
        $params = [
            'agency_name'  => $data['agency_name'] ?? 'Your Agency',
            'client_name'  => $data['client_name'],
            'contact_name' => $data['contact_name'] ?? $data['client_name'],
            'niche'        => $data['niche'],
            'tier'         => $data['tier'],
            'price'        => $data['price'],
            'services'     => $data['services'] ?? [],
            'notes'        => $data['notes'] ?? '',
        ];

        $content = $this->proposalService->generateTemplate($params);
        $this->auditLog->log($userId, 'proposal.generate', 'proposal', null, ['mode' => 'template']);

        return $this->response->setJSON([
            'data' => ['content' => $content, 'generation_mode' => 'template'],
            'error' => null,
        ]);
    }

    public function save(): ResponseInterface
    {
        $userId = session()->get('user_id');
        $data   = $this->request->getJSON(true) ?? [];

        $rules = [
            'client_name' => 'required|min_length[2]',
            'tier'        => 'required|in_list[starter,growth,premium,custom]',
            'price'       => 'required|integer|greater_than[0]',
            'content'     => 'required',
        ];
        $validation = \Config\Services::validation();
        $validation->setRules($rules);
        if (!$validation->run($data)) {
            return $this->response->setStatusCode(422)->setJSON([
                'data' => null, 'error' => implode(' ', $validation->getErrors()),
            ]);
        }

        // Sanitize content - allow safe HTML tags only
        $allowedTags = '<h2><h3><p><ul><li><strong><em><table><thead><tbody><tr><th><td><div><br><hr>';
        $content = strip_tags($data['content'], $allowedTags);

        $id = $this->model->insert([
            'user_id'         => $userId,
            'prospect_id'     => $data['prospect_id'] ?? null,
            'package_id'      => $data['package_id'] ?? null,
            'agency_name'     => $data['agency_name'] ?? '',
            'client_name'     => $data['client_name'],
            'contact_name'    => $data['contact_name'] ?? '',
            'niche'           => $data['niche'] ?? '',
            'tier'            => $data['tier'],
            'price'           => (int) $data['price'],
            'services'        => json_encode($data['services'] ?? []),
            'content'         => $content,
            'generation_mode' => $data['generation_mode'] ?? 'template',
            'notes'           => $data['notes'] ?? '',
            'valid_until'     => date('Y-m-d', strtotime('+14 days')),
            'status'          => 'draft',
        ]);

        if (!$id) {
            return $this->response->setStatusCode(500)->setJSON([
                'data' => null, 'error' => 'Failed to save proposal.',
            ]);
        }

        $this->auditLog->log($userId, 'proposal.save', 'proposal', $id);
        return $this->response->setStatusCode(201)->setJSON([
            'data'  => ['id' => $id, 'message' => 'Proposal saved.'],
            'error' => null,
        ]);
    }
}
