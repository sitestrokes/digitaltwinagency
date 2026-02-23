<?php

namespace App\Models;

use CodeIgniter\Model;

class ProspectModel extends Model
{
    protected $table            = 'prospects';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id', 'name', 'niche', 'website_status', 'video_status',
        'social_status', 'budget', 'competitors', 'score',
        'readiness_level', 'pain_points', 'notes'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $casts = [
        'pain_points' => 'json-array',
    ];

    public function getByUser(int $userId, int $limit = 20, int $offset = 0): array
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll($limit, $offset);
    }

    public function getRecentByUser(int $userId, int $limit = 5): array
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll($limit);
    }

    public function getByUserAndId(int $userId, int $id): ?array
    {
        return $this->where('user_id', $userId)->where('id', $id)->first();
    }

    public function deleteByUserAndId(int $userId, int $id): bool
    {
        return $this->where('user_id', $userId)->where('id', $id)->delete();
    }

    public function countByUser(int $userId): int
    {
        return $this->where('user_id', $userId)->countAllResults();
    }
}
