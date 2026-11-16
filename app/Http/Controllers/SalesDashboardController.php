<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectPayment;
use Illuminate\Http\Request;

class SalesDashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();

        // 1. Assigned Projects IDs fetch kar li
        $assignedProjectIds = Project::where('sales_officer_id', $userId)->pluck('id');

        // 2. Stats & Metrics
        $totalDeals = Project::where('sales_officer_id', $userId)->count();
        $activeDeals = Project::where('sales_officer_id', $userId)->where('status', 'active')->count();

        $totalSalesAmount = ProjectPayment::whereIn('project_id', $assignedProjectIds)
            ->when($request->from, fn($q, $v) => $q->whereDate('payment_date', '>=', $v))
            ->when($request->to, fn($q, $v) => $q->whereDate('payment_date', '<=', $v))
            ->sum('payment_amount');

        $totalCommission = ProjectPayment::whereIn('project_id', $assignedProjectIds)
            ->when($request->from, fn($q, $v) => $q->whereDate('payment_date', '>=', $v))
            ->when($request->to, fn($q, $v) => $q->whereDate('payment_date', '<=', $v))
            ->sum('sales_commission_amount');

        // 3. Assigned Projects List
        $projects = Project::where('sales_officer_id', $userId)->with('client')->latest()->get();

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

        return view('sales.dashboard', compact(
            'totalDeals',
            'activeDeals',
            'totalSalesAmount',
            'totalCommission',
            'projects',
            'clients',
            'payments'
        ));
    }
}