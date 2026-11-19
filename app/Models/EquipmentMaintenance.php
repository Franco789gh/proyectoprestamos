<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentMaintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_id',
        'user_id',
        'entry_date',
        'exit_date',
        'provider',
        'reason',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'entry_date' => 'datetime',
            'exit_date' => 'datetime',
        ];
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
