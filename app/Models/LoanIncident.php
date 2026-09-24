<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanIncident extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'incident_type',
        'description',
        'resolution_notes',
        'status',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
