<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecurringPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'recurring_amount',
        'billing_frequency',
        'recurring_start_date',
        'next_payment_date',
        'status',
    ];

    protected $casts = [
        'recurring_amount' => 'decimal:2',
        'recurring_start_date' => 'date',
        'next_payment_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function payments()
    {
        return $this->hasMany(ProjectPayment::class);
    }
}