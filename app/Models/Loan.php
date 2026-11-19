<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_code',
        'worker_id',
        'equipment_id',
        'loan_user_id',
        'return_user_id',
        'loan_date',
        'expected_return_date',
        'extensions_count',
        'actual_return_date',
        'purpose',
        'loan_notes',
        'return_notes',
        'equipment_return_condition',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'loan_date' => 'datetime',
            'expected_return_date' => 'datetime',
            'actual_return_date' => 'datetime',
        ];
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function loanUser()
    {
        return $this->belongsTo(User::class, 'loan_user_id');
    }

    public function returnUser()
    {
        return $this->belongsTo(User::class, 'return_user_id');
    }

    public function loanAccessories()
    {
        return $this->hasMany(LoanAccessory::class);
    }

    public function accessories()
    {
        return $this->belongsToMany(Accessory::class, 'loan_accessories')
            ->withPivot('was_delivered', 'was_returned', 'observation')
            ->withTimestamps();
    }

    public function incidents()
    {
        return $this->hasMany(LoanIncident::class);
    }

    public function incident()
    {
        return $this->hasOne(LoanIncident::class)->latestOfMany();
    }

    public function isOverdue(): bool
    {
        return $this->status === 'activo' && $this->expected_return_date->isPast();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'activo');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'activo')
            ->where('expected_return_date', '<', Carbon::now());
    }

    public function scopeDueToday($query)
    {
        return $query->where('status', 'activo')
            ->whereDate('expected_return_date', Carbon::today());
    }

    public static function generateLoanCode(): string
    {
        $year = date('Y');
        $prefix = "PRE-{$year}-";
        $last = static::where('loan_code', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->value('loan_code');

        if ($last) {
            $number = (int) substr($last, strlen($prefix)) + 1;
        } else {
            $number = 1;
        }

        return $prefix . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
