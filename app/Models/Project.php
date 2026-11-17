<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
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

    #[Scope]
    protected function visibleTo(Builder $query, User $user): void
    {
        if ($user->hasRole('Super Admin')) {
            return;
        }

        $isSalesOfficer = $user->hasRole('Sales Officer');
        $isVa = $user->hasRole('VA');

        if ($isSalesOfficer && $isVa) {
            $query->where(function (Builder $query) use ($user): void {
                $query->where('sales_officer_id', $user->getKey())
                    ->orWhereHas('vas', fn (Builder $query) => $query->whereKey($user->getKey()));
            });

            return;
        }

        if ($isSalesOfficer) {
            $query->where('sales_officer_id', $user->getKey());

            return;
        }

        if ($isVa) {
            $query->whereHas('vas', fn (Builder $query) => $query->whereKey($user->getKey()));

            return;
        }

        $query->whereRaw('1 = 0');
    }

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

    // Recurring Payment relationship
    public function recurringPayment()
    {
        return $this->hasOne(RecurringPayment::class);
    }

    // Payments relationship
    public function payments()
    {
        return $this->hasMany(ProjectPayment::class);
    }
}
