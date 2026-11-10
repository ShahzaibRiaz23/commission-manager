<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'client_id',
        'sales_officer_id',
        'project_amount',
        'payment_type',
        'va_commission_percentage',
        'sales_commission_percentage',
        'status',
        'start_date',
        'deadline',
    ];

    protected $casts = [
        'project_amount' => 'decimal:2',
        'va_commission_percentage' => 'decimal:2',
        'sales_commission_percentage' => 'decimal:2',
        'start_date' => 'date',
        'deadline' => 'date',
    ];

    // Client relationship 
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Sales Officer relationship 
    public function salesOfficer()
    {
        return $this->belongsTo(User::class, 'sales_officer_id');
    }

    // VAs relationship
    public function vas()
    {
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id')
                    ->withPivot('custom_commission_percentage')
                    ->withTimestamps();
    }
}