<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table            = 'audit_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id', 'action', 'entity_type', 'entity_id',
        'meta', 'ip_address', 'user_agent'
    ];

    protected $useTimestamps  = true;
    protected $updatesTimestamp = false;
    protected $dateFormat     = 'datetime';
    protected $createdField   = 'created_at';

    protected array $casts = [
        'meta' => '?json-array',
    ];

    public function log(
        ?int $userId,
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        ?array $meta = null
    ): void {
        $request = service('request');
        $this->insert([
            'user_id'     => $userId,
            'action'      => $action,
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'meta'        => $meta ? json_encode($meta) : null,
            'ip_address'  => $request->getIPAddress(),
            'user_agent'  => substr((string)$request->getUserAgent(), 0, 500),
        ]);
    }

    public function countByUserAndAction(int $userId, string $action, string $since): int
    {
        return $this->where('user_id', $userId)
                    ->where('action', $action)
                    ->where('created_at >=', $since)
                    ->countAllResults();
    }
}
