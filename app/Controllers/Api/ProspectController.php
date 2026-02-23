<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ProspectModel;
use App\Models\AuditLogModel;
use CodeIgniter\HTTP\ResponseInterface;

class ProspectController extends BaseController
{
    protected ProspectModel $model;
    protected AuditLogModel $auditLog;

    public function __construct()
    {
        $this->model    = new ProspectModel();
        $this->auditLog = new AuditLogModel();
    }

    public function index(): ResponseInterface
    {
        $userId = session()->get('user_id');
        $prospects = $this->model->getByUser($userId, 20);
        return $this->response->setJSON(['data' => $prospects, 'error' => null]);
    }

    public function save(): ResponseInterface
    {
        $userId = session()->get('user_id');
        $data   = $this->request->getJSON(true) ?? [];

        $rules = [
            'name'            => 'required|min_length[2]|max_length[255]',
            'niche'           => 'required|max_length[100]',
            'score'           => 'required|integer|greater_than_equal_to[0]|less_than_equal_to[100]',
            'readiness_level' => 'required|in_list[hot,warm,cold]',
        ];

        $validation = \Config\Services::validation();
        $validation->setRules($rules);
        if (!$validation->run($data)) {
            return $this->response->setStatusCode(422)->setJSON([
                'data'  => null,
                'error' => implode(' ', $validation->getErrors()),
            ]);
        }

        $toInsert = [
            'user_id'         => $userId,
            'name'            => $data['name'],
            'niche'           => $data['niche'],
            'website_status'  => $data['website_status'] ?? null,
            'video_status'    => $data['video_status'] ?? null,
            'social_status'   => $data['social_status'] ?? null,
            'budget'          => $data['budget'] ?? null,
            'competitors'     => $data['competitors'] ?? null,
            'score'           => (int) $data['score'],
            'readiness_level' => $data['readiness_level'],
            'pain_points'     => json_encode($data['pain_points'] ?? []),
            'notes'           => $data['notes'] ?? null,
        ];

        $id = $this->model->insert($toInsert);
        if (!$id) {
            return $this->response->setStatusCode(500)->setJSON([
                'data'  => null,
                'error' => 'Failed to save prospect.',
            ]);
        }

        $this->auditLog->log($userId, 'prospect.save', 'prospect', $id);

        return $this->response->setStatusCode(201)->setJSON([
            'data'  => ['id' => $id, 'message' => 'Prospect saved successfully.'],
            'error' => null,
        ]);
    }

    public function delete(int $id): ResponseInterface
    {
        $userId = session()->get('user_id');
        $deleted = $this->model->deleteByUserAndId($userId, $id);
        if (!$deleted) {
            return $this->response->setStatusCode(404)->setJSON([
                'data'  => null,
                'error' => 'Prospect not found.',
            ]);
        }
        $this->auditLog->log($userId, 'prospect.delete', 'prospect', $id);
        return $this->response->setJSON(['data' => ['deleted' => true], 'error' => null]);
    }
}
