<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectPayment;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // 1. Admin/Owner Dashboard Logic
        if ($user->hasRole('Super Admin') || $user->hasRole('Admin') || $user->hasRole('Owner')) {

            // Querying ProjectPayment model
            $payments = ProjectPayment::query()
                ->when($request->from, fn($q, $v) => $q->whereDate('payment_date', '>=', $v))
                ->when($request->to, fn($q, $v) => $q->whereDate('payment_date', '<=', $v))
                ->when($request->payment_type, fn($q, $v) => $q->where('payment_type', $v))
                ->when($request->project_id, fn($q, $v) => $q->where('project_id', $v))
                ->whereHas('project', function ($q) use ($request) {
                    $q->when($request->client_id, fn($q, $v) => $q->where('client_id', $v))
                        ->when($request->status, fn($q, $v) => $q->where('status', $v))
                        ->when($request->sales_officer_id, fn($q, $v) => $q->where('sales_officer_id', $v))
                        ->when($request->va_id, function ($q, $vaId) {
                            $q->whereHas('vas', fn($subQ) => $subQ->where('users.id', $vaId));
                        });
                });

            // Precision Decimal Calculation Helpers
            $sum = fn($query, $col) => (string) ((clone $query)->sum($col) ?: '0');

            $totalProjectRevenue = $sum((clone $payments)->where('payment_type', 'one-time'), 'payment_amount');
            $totalRecurringRevenue = $sum((clone $payments)->where('payment_type', 'recurring'), 'payment_amount');
            $totalVaCommissions = $sum($payments, 'va_commission_amount');
            $totalSalesCommissions = $sum($payments, 'sales_commission_amount');

            $recurringCommission = (clone $payments)->where('payment_type', 'recurring')
                ->selectRaw('COALESCE(SUM(va_commission_amount + sales_commission_amount), 0) as t')->value('t');

            $pendingCommissions = (clone $payments)->where('status', 'pending')
                ->selectRaw('COALESCE(SUM(va_commission_amount + sales_commission_amount), 0) as t')->value('t');

            $totalRemainingRevenue = bcsub(
                bcadd($totalProjectRevenue, $totalRecurringRevenue, 2),
                bcadd($totalVaCommissions, $totalSalesCommissions, 2),
                2
            );

            // Monthly breakdown calculation
            $monthly = (clone $payments)
                ->selectRaw("
                    DATE_FORMAT(payment_date, '%Y-%m') as ym,
                    DATE_FORMAT(payment_date, '%b %Y') as month,
                    SUM(payment_amount) as revenue,
                    SUM(va_commission_amount + sales_commission_amount) as commission
                ")
                ->groupBy('ym', 'month')
                ->orderBy('ym')
                ->get()
                ->map(fn($r) => [
                    'month' => $r->month ?? date('M Y'),
                    'revenue' => (float) $r->revenue,
                    'commission' => (float) $r->commission
                ])
                ->values();

            // Project Breakdown
            $projectBreakdown = (clone $payments)->with('project:id,name')->get()
                ->groupBy('project_id')
                ->map(fn($rows) => [
                    'name' => optional($rows->first()->project)->name ?? 'Unassigned Project',
                    'commission' => $rows->sum(fn($p) => $p->va_commission_amount + $p->sales_commission_amount),
                ])->sortByDesc('commission')->take(8)->values();

            $salesBreakdown = $this->byPerson($payments, 'sales_officer_id', 'sales_commission_amount');

            // VA Breakdown using pivot relation
            $vaBreakdown = ProjectPayment::with('project.vas')
                ->when($request->from, fn($q, $v) => $q->whereDate('payment_date', '>=', $v))
                ->when($request->to, fn($q, $v) => $q->whereDate('payment_date', '<=', $v))
                ->get()
                ->flatMap(function ($payment) {
                    return $payment->project->vas->map(fn($va) => [
                        'va_id' => $va->id,
                        'name' => $va->name,
                        'commission' => $payment->va_commission_amount / max(1, $payment->project->vas->count())
                    ]);
                })
                ->groupBy('va_id')
                ->map(fn($rows) => [
                    'name' => $rows->first()['name'],
                    'commission' => $rows->sum('commission')
                ])
                ->sortByDesc('commission')
                ->take(8)
                ->values();

            return view('dashboard.admin', [
                'totalProjects' => Project::count(),
                'activeProjects' => Project::where('status', 'active')->count(),
                'totalClients' => Client::count(),
                'totalProjectRevenue' => $totalProjectRevenue,
                'totalRecurringRevenue' => $totalRecurringRevenue,
                'totalVaCommissions' => $totalVaCommissions,
                'totalSalesCommissions' => $totalSalesCommissions,
                'totalRemainingRevenue' => $totalRemainingRevenue,
                'pendingCommissions' => $pendingCommissions,
                'recurringCommission' => $recurringCommission,
                'totalEarnedCommission' => bcadd($totalVaCommissions, $totalSalesCommissions, 2),
                'monthly' => $monthly,
                'vaBreakdown' => $vaBreakdown,
                'salesBreakdown' => $salesBreakdown,
                'projectBreakdown' => $projectBreakdown,
                'recentPayments' => (clone $payments)->with('project.client')->latest('payment_date')->take(8)->get(),

                'projects' => Project::orderBy('name')->get(['id', 'name']),
                'clients' => Client::orderBy('name')->get(['id', 'name']),
                'vas' => User::role('VA')->orderBy('name')->get(['id', 'name']),
                'salesOfficers' => User::role('Sales Officer')->orderBy('name')->get(['id', 'name']),
            ]);
        }

        // 2. VA / Sales Officer User Dashboard Logic
        $assignedProjects = Project::whereHas('vas', fn($q) => $q->where('users.id', $user->id))
            ->orWhere('sales_officer_id', $user->id)
            ->with('client')
            ->get();

        $userPayments = ProjectPayment::whereIn('project_id', $assignedProjects->pluck('id'))->get();

        $totalEarnedCommission = $userPayments->sum(function ($p) use ($user) {
            $sum = 0;
            if ($p->project->vas->contains($user->id)) {
                $vaCount = max(1, $p->project->vas->count());
                $sum += ($p->va_commission_amount / $vaCount);
            }
            if ($p->project->sales_officer_id == $user->id) {
                $sum += $p->sales_commission_amount;
            }
            return $sum;
        });

        return view('dashboard.user', [
            'assignedProjectsCount' => $assignedProjects->count(),
            'totalEarnedCommission' => $totalEarnedCommission,
            'assignedProjects' => $assignedProjects,
        ]);
    }

    /** Sum a commission column per person, reading the person id from the project. */
    private function byPerson($payments, string $projectColumn, string $amountColumn)
    {
        return (clone $payments)->with('project:id,' . $projectColumn)->get()
            ->groupBy(fn($p) => optional($p->project)->{$projectColumn})
            ->filter(fn($rows, $userId) => !empty($userId))
            ->map(fn($rows, $userId) => [
                'name' => optional(User::find($userId))->name ?? 'Unassigned',
                'commission' => $rows->sum($amountColumn),
            ])->sortByDesc('commission')->take(8)->values();
    }
}