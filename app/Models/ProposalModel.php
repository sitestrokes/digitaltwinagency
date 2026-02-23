<?php

namespace App\Models;

use CodeIgniter\Model;

class ProposalModel extends Model
{
    protected $table            = 'proposals';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id', 'prospect_id', 'package_id', 'agency_name',
        'client_name', 'contact_name', 'niche', 'tier', 'price',
        'services', 'content', 'generation_mode', 'notes',
        'valid_until', 'status'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected array $casts = [
        'services' => 'json-array',
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

    public function countAiGenerationsToday(int $userId): int
    {
        return $this->where('user_id', $userId)
                    ->where('generation_mode', 'ai')
                    ->where('created_at >=', date('Y-m-d 00:00:00'))
                    ->countAllResults();
    }

    public function deleteByUserAndId(int $userId, int $id): bool
    {
        return $this->where('user_id', $userId)->where('id', $id)->delete();
    }
}
