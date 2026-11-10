<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'recurring_payment_id',
        'payment_amount',
        'payment_date',
        'payment_type',
        'va_commission_percentage',
        'va_commission_amount',
        'sales_commission_percentage',
        'sales_commission_amount',
        'remaining_revenue',
        'status',
        'transaction_reference',
    ];

    protected $casts = [
        'payment_amount' => 'decimal:2',
        'va_commission_percentage' => 'decimal:2',
        'va_commission_amount' => 'decimal:2',
        'sales_commission_percentage' => 'decimal:2',
        'sales_commission_amount' => 'decimal:2',
        'remaining_revenue' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}