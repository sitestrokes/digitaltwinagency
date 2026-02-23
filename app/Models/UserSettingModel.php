<?php

namespace App\Models;

use CodeIgniter\Model;

class UserSettingModel extends Model
{
    protected $table            = 'user_settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id', 'openai_api_key', 'openai_model',
        'agency_name', 'agency_email', 'agency_phone'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getByUserId(int $userId): ?array
    {
        return $this->where('user_id', $userId)->first();
    }

    public function upsertForUser(int $userId, array $data): bool
    {
        $existing = $this->getByUserId($userId);
        if ($existing) {
            return $this->update($existing['id'], $data);
        }
        $data['user_id'] = $userId;
        return (bool) $this->insert($data);
    }

    public function hasApiKey(int $userId): bool
    {
        $row = $this->getByUserId($userId);
        return !empty($row['openai_api_key']);
    }
}
