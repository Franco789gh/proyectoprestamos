<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'equipment';

    protected $fillable = [
        'code',
        'name',
        'brand',
        'model',
        'serial_number',
        'category',
        'physical_condition',
        'status',
        'notes',
    ];

    public function accessories()
    {
        return $this->hasMany(Accessory::class);
    }

    public function activeAccessories()
    {
        return $this->hasMany(Accessory::class)->where('is_active', true);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class)->orderBy('created_at', 'desc');
    }

    public function currentLoan()
    {
        return $this->hasOne(Loan::class)->where('status', 'activo')->latestOfMany();
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'disponible');
    }

    public function isAvailable(): bool
    {
        return $this->status === 'disponible';
    }
}
