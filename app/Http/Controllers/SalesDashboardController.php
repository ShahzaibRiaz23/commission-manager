<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\ProjectPayment;

class SalesDashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

      
        $totalDeals = Project::where('sales_officer_id', $userId)->count();
        $activeDeals = Project::where('sales_officer_id', $userId)->where('status', 'active')->count();

        $totalSalesAmount = ProjectPayment::whereHas('project', function($query) use ($userId) {
            $query->where('sales_officer_id', $userId);
        })->sum('amount');

        $totalCommission = ProjectPayment::whereHas('project', function($query) use ($userId) {
            $query->where('sales_officer_id', $userId);
        })->sum('sales_commission');

        $projects = Project::where('sales_officer_id', $userId)->with('client')->latest()->get();

        return view('sales.dashboard', compact(
            'totalDeals', 
            'activeDeals', 
            'totalSalesAmount', 
            'totalCommission', 
            'projects'
        ));
    }
}