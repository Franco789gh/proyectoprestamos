<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'description',
        'payload_before',
        'payload_after',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'payload_before' => 'array',
            'payload_after' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function logAction(string $action, string $description, $entity = null, ?array $payloadBefore = null, ?array $payloadAfter = null): self
    {
        return static::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'entity_type' => $entity ? get_class($entity) : null,
            'entity_id' => $entity?->id,
            'description' => $description,
            'payload_before' => $payloadBefore,
            'payload_after' => $payloadAfter,
            'ip_address' => Request::ip(),
        ]);
    }
}
