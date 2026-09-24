<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanAccessory extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'accessory_id',
        'was_delivered',
        'was_returned',
        'observation',
    ];

    protected function casts(): array
    {
        return [
            'was_delivered' => 'boolean',
            'was_returned' => 'boolean',
        ];
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function accessory()
    {
        return $this->belongsTo(Accessory::class);
    }
}
