<?php

namespace App\Models;

use CodeIgniter\Model;

class PackageModel extends Model
{
    protected $table            = 'packages';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id', 'name', 'selected_services',
        'starter_price', 'growth_price', 'premium_price',
        'starter_services', 'growth_services', 'premium_services'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected array $casts = [
        'selected_services' => '?json-array',
        'starter_services'  => '?json-array',
        'growth_services'   => '?json-array',
        'premium_services'  => '?json-array',
    ];

    public function getByUser(int $userId, int $limit = 20): array
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
}
