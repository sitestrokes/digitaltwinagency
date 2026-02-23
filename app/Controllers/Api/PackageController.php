<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\PackageModel;
use App\Models\AuditLogModel;
use CodeIgniter\HTTP\ResponseInterface;

class PackageController extends BaseController
{
    protected PackageModel $model;
    protected AuditLogModel $auditLog;

    public function __construct()
    {
        $this->model    = new PackageModel();
        $this->auditLog = new AuditLogModel();
    }

    public function index(): ResponseInterface
    {
        $userId = session()->get('user_id');
        return $this->response->setJSON([
            'data'  => $this->model->getByUser($userId),
            'error' => null,
        ]);
    }

    public function save(): ResponseInterface
    {
        $userId = session()->get('user_id');
        $data   = $this->request->getJSON(true) ?? [];

        $rules = [
            'name'          => 'required|min_length[2]|max_length[255]',
            'starter_price' => 'required|integer|greater_than[0]',
            'growth_price'  => 'required|integer|greater_than[0]',
            'premium_price' => 'required|integer|greater_than[0]',
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
            'user_id'           => $userId,
            'name'              => $data['name'],
            'selected_services' => json_encode($data['selected_services'] ?? []),
            'starter_price'     => (int) $data['starter_price'],
            'growth_price'      => (int) $data['growth_price'],
            'premium_price'     => (int) $data['premium_price'],
            'starter_services'  => json_encode($data['starter_services'] ?? []),
            'growth_services'   => json_encode($data['growth_services'] ?? []),
            'premium_services'  => json_encode($data['premium_services'] ?? []),
        ];

        $id = $this->model->insert($toInsert);
        if (!$id) {
            return $this->response->setStatusCode(500)->setJSON([
                'data'  => null,
                'error' => 'Failed to save package.',
            ]);
        }

        $this->auditLog->log($userId, 'package.save', 'package', $id);
        return $this->response->setStatusCode(201)->setJSON([
            'data'  => ['id' => $id, 'message' => 'Package saved successfully.'],
            'error' => null,
        ]);
    }

    public function delete(int $id): ResponseInterface
    {
        $userId  = session()->get('user_id');
        $deleted = $this->model->deleteByUserAndId($userId, $id);
        if (!$deleted) {
            return $this->response->setStatusCode(404)->setJSON([
                'data' => null, 'error' => 'Package not found.',
            ]);
        }
        $this->auditLog->log($userId, 'package.delete', 'package', $id);
        return $this->response->setJSON(['data' => ['deleted' => true], 'error' => null]);
    }
}
