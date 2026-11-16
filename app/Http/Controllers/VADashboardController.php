<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectPayment;
use Illuminate\Http\Request;

class VADashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();

        // 1. Assigned Projects Query Builder
        $assignedProjects = Project::whereHas('vas', function ($query) use ($userId) {
            $query->where('users.id', $userId);
        });

        $assignedProjectIds = (clone $assignedProjects)->pluck('id');

        // 2. Stats & Metrics
        $totalProjects = (clone $assignedProjects)->count();
        $activeProjects = (clone $assignedProjects)->where('status', 'active')->count();
        $pendingProjects = (clone $assignedProjects)->where('status', 'pending')->count();
        
        // 3. Assigned Projects List
        $projects = (clone $assignedProjects)->with('client')->latest()->get();

        // 4. Assigned Clients 
        $clientIds = $projects->pluck('client_id')->unique();
        $clients = Client::whereIn('id', $clientIds)->get();

        // 5. Payments / Ledger 
        $payments = ProjectPayment::whereIn('project_id', $assignedProjectIds)
            ->when($request->from, fn($q, $v) => $q->whereDate('payment_date', '>=', $v))
            ->when($request->to, fn($q, $v) => $q->whereDate('payment_date', '<=', $v))
            ->with('project.client')
            ->latest('payment_date')
            ->get();

        // 6. Total Commission Calculation
        $totalCommission = ProjectPayment::with('project.vas')
            ->whereIn('project_id', $assignedProjectIds)
            ->when($request->from, fn($q, $v) => $q->whereDate('payment_date', '>=', $v))
            ->when($request->to, fn($q, $v) => $q->whereDate('payment_date', '<=', $v))
            ->get()
            ->sum(function ($payment) {
                return $payment->va_commission_amount / max(1, $payment->project->vas->count());
            });

        return view('va.dashboard', compact(
            'totalProjects',
            'activeProjects',
            'pendingProjects',
            'projects',
            'clients',
            'payments',
            'totalCommission'
        ));
    }
}