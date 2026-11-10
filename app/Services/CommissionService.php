<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectPayment;
use Illuminate\Support\Facades\DB;

class CommissionService
{
    /**
     * Record a payment and automatically calculate commissions using project snapshots.
     */
    public function recordPayment(Project $project, array $data)
    {
        return DB::transaction(function () use ($project, $data) {
            $paymentAmount = (float) $data['payment_amount'];
            
            // Use project-level commission percentages (snapshot rule)
            $vaPercentage = (float) $project->va_commission_percentage;
            $salesPercentage = (float) $project->sales_commission_percentage;

            // Calculate commission amounts with precision
            $vaCommissionAmount = round(($paymentAmount * $vaPercentage) / 100, 2);
            $salesCommissionAmount = round(($paymentAmount * $salesPercentage) / 100, 2);
            
            // Calculate remaining revenue
            $remainingRevenue = round($paymentAmount - ($vaCommissionAmount + $salesCommissionAmount), 2);

            // Save the payment and commission transaction ledger
            return ProjectPayment::create([
                'project_id' => $project->id,
                'recurring_payment_id' => $data['recurring_payment_id'] ?? null,
                'payment_amount' => $paymentAmount,
                'payment_date' => $data['payment_date'],
                'payment_type' => $data['payment_type'], // 'one-time' or 'recurring'
                'va_commission_percentage' => $vaPercentage,
                'va_commission_amount' => $vaCommissionAmount,
                'sales_commission_percentage' => $salesPercentage,
                'sales_commission_amount' => $salesCommissionAmount,
                'remaining_revenue' => $remainingRevenue,
                'status' => $data['status'] ?? 'pending',
                'transaction_reference' => $data['transaction_reference'] ?? null,
            ]);
        });
    }
}