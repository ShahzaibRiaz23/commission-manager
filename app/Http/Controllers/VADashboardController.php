<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectPayment;

class VADashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $assignedProjects = Project::whereHas('vas', function ($query) use ($userId) {
            $query->where('users.id', $userId);
        });

        $totalProjects = (clone $assignedProjects)->count();
        $activeProjects = (clone $assignedProjects)->where('status', 'active')->count();
        $pendingProjects = (clone $assignedProjects)->where('status', 'pending')->count();
        $projects = (clone $assignedProjects)->with('client')->latest()->get();

        $totalCommission = ProjectPayment::with('project.vas')
            ->whereHas('project.vas', function ($query) use ($userId) {
                $query->where('users.id', $userId);
            })
            ->get()
            ->sum(function ($payment) {
                return $payment->va_commission_amount / max(1, $payment->project->vas->count());
            });

        return view('va.dashboard', compact(
            'totalProjects',
            'activeProjects',
            'pendingProjects',
            'projects',
            'totalCommission'
        ));
    }
}
