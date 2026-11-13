<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\ProjectPayment;

class VADashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

      
        $totalProjects = Project::where('va_id', $userId)->count();
        $activeProjects = Project::where('va_id', $userId)->where('status', 'active')->count();
        $pendingProjects = Project::where('va_id', $userId)->where('status', 'pending')->count();

        $projects = Project::where('va_id', $userId)->with('client')->latest()->get();

  
        $totalCommission = ProjectPayment::whereHas('project', function ($query) use ($userId) {
            $query->where('va_id', $userId);
        })->sum('va_commission');

        return view('va.dashboard', compact(
            'totalProjects',
            'activeProjects',
            'pendingProjects',
            'projects',
            'totalCommission'
        ));
    }
}