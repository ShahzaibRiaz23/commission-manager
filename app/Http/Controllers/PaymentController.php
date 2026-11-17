<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectPayment;
use App\Models\User;
use App\Services\CommissionService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentController extends Controller
{
    protected $commissionService;

    public function create(Project $project)
    {
        return view('payments.create', compact('project'));
    }

    public function store(Request $request, Project $project, CommissionService $commissionService)
    {
        $validated = $request->validate([
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_type' => 'required|in:one-time,recurring',
            'status' => 'required|in:pending,approved,paid',
            'transaction_reference' => 'nullable|string|max:255',
            'recurring_payment_id' => 'nullable|exists:recurring_payments,id',
        ]);

        // Pass the validated data including status to the service
        $commissionService->recordPayment($project, $validated);

        return redirect()->route('projects.show', $project->id)->with('success', 'Payment recorded and commissions calculated successfully.');
    }

    // List all financial payment transactions (Role-based scoped)
    public function index()
    {
        $user = auth()->user();
        $query = $this->scopePaymentsForUser(ProjectPayment::with(['project.client']), $user);
        $payments = $query->latest('payment_date')->paginate(15);

        return view('payments.index', compact('payments'));
    }

    // Update payout status (Pending -> Approved -> Paid)
    public function updateStatus(Request $request, ProjectPayment $payment)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,paid',
        ]);

        $payment->update($validated);

        return back()->with('success', 'Payment status updated successfully.');
    }

    // Export filtered payments ledger to CSV
    public function export(): StreamedResponse
    {
        $user = auth()->user();
        $fileName = 'payments_ledger_'.date('Y-m-d').'.csv';

        $query = $this->scopePaymentsForUser(ProjectPayment::with(['project.client']), $user);
        $payments = $query->latest('payment_date')->get();

        $callback = function () use ($payments) {
            $file = fopen('php://output', 'w');

            // CSV Header Row
            fputcsv($file, ['ID', 'Date', 'Project Name', 'Client', 'Payment Amount', 'Experts Commission', 'Sales Commission', 'Remaining Revenue', 'Status']);

            // CSV Data Rows
            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->id,
                    optional($payment->payment_date)->format('Y-m-d') ?? $payment->created_at->format('Y-m-d'),
                    $payment->project->name ?? 'N/A',
                    $payment->project->client->name ?? 'N/A',
                    $payment->payment_amount,
                    $payment->va_commission_amount,
                    $payment->sales_commission_amount,
                    $payment->remaining_revenue,
                    $payment->status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }

    private function scopePaymentsForUser(Builder $query, User $user): Builder
    {
        if ($user->hasRole('Super Admin')) {
            return $query;
        }

        if ($user->hasRole('Sales Officer')) {
            $projectIds = Project::where('sales_officer_id', $user->id)->pluck('id');

            return $query->whereIn('project_id', $projectIds);
        }

        if ($user->hasRole('VA')) {
            $projectIds = Project::whereHas('vas', fn ($query) => $query->where('users.id', $user->id))
                ->pluck('id');

            return $query->whereIn('project_id', $projectIds);
        }

        return $query->whereRaw('1 = 0');
    }
}
