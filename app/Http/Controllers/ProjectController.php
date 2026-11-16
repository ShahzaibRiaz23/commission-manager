<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // List all projects with pagination and relationships (Role-based scope)
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('Super Admin')) {
            $projects = Project::with(['client', 'salesOfficer', 'vas'])->latest()->paginate(10);
        } elseif ($user->hasRole('Sales Officer')) {
            $projects = Project::where('sales_officer_id', $user->id)
                ->with(['client', 'salesOfficer', 'vas'])
                ->latest()
                ->paginate(10);
        } elseif ($user->hasRole('VA')) {
            $projects = Project::whereHas('vas', fn($q) => $q->where('users.id', $user->id))
                ->with(['client', 'salesOfficer', 'vas'])
                ->latest()
                ->paginate(10);
        } else {
            $projects = Project::whereRaw('1 = 0')->paginate(10);
        }

        return view('projects.index', compact('projects'));
    }

    // Show form to create a project
    public function create()
    {
        $clients = Client::orderBy('name')->get();
        $vas = User::role('VA')->orderBy('name')->get();
        $salesOfficers = User::role('Sales Officer')->orderBy('name')->get();

        return view('projects.create', compact('clients', 'vas', 'salesOfficers'));
    }

    // Store a new project
    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'client_id' => 'required|exists:clients,id',
            'sales_officer_id' => 'nullable|exists:users,id',
            'project_amount' => 'required|numeric|min:0',
            'payment_type' => 'required|in:one-time,recurring',
            'va_commission_percentage' => 'nullable|numeric|min:0|max:100',
            'sales_commission_percentage' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:pending,active,on_hold,completed,cancelled',
            'start_date' => 'nullable|date',
            'deadline' => 'nullable|date',
            'vas' => 'nullable|array',
            'vas.*' => 'exists:users,id',
        ]);

        // Sales Officer restriction & auto-assignment
        if ($user->hasRole('Sales Officer') && ! $user->hasRole('Super Admin')) {
            $validated['sales_officer_id'] = $user->id;
            // Prevent commission manipulation from request payload
            unset($validated['va_commission_percentage'], $validated['sales_commission_percentage']);
        }

        // Create the project
        $project = Project::create($validated);

        // Attach VAs if selected by Super Admin
        if ($user->hasRole('Super Admin') && !empty($validated['vas'])) {
            $project->vas()->attach($validated['vas']);
        }

        return redirect()->route('projects.index')->with('success', 'Project created successfully.');
    }

    // Show project details
    public function show(Project $project)
    {
        $project->load(['client', 'salesOfficer', 'vas', 'recurringPayment', 'payments']);
        return view('projects.show', compact('project'));
    }

    // Show form to edit project
    public function edit(Project $project)
    {
        $clients = Client::orderBy('name')->get();
        $vas = User::role('VA')->orderBy('name')->get();
        $salesOfficers = User::role('Sales Officer')->orderBy('name')->get();
        $users = User::all();

        $project->load('vas');

        return view('projects.edit', compact('project', 'clients', 'vas', 'salesOfficers', 'users'));
    }

    // Update project
    public function update(Request $request, Project $project)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'client_id' => 'required|exists:clients,id',
            'sales_officer_id' => 'nullable|exists:users,id',
            'project_amount' => 'required|numeric|min:0',
            'payment_type' => 'required|in:one-time,recurring',
            'va_commission_percentage' => 'nullable|numeric|min:0|max:100',
            'sales_commission_percentage' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:active,completed,cancelled,pending,on_hold',
            'start_date' => 'nullable|date',
            'deadline' => 'nullable|date',
            'vas' => 'nullable|array',
            'vas.*' => 'exists:users,id',
        ]);

        // Only non-admin Sales Officers are restricted to assigning themselves.
        if ($user->hasRole('Sales Officer') && ! $user->hasRole('Super Admin')) {
            $validated['sales_officer_id'] = $user->id;
            unset($validated['va_commission_percentage'], $validated['sales_commission_percentage']);
        }

        $project->update($validated);

        // Sync VAs pivot table only for Super Admin
        if ($user->hasRole('Super Admin')) {
            if (isset($validated['vas'])) {
                $project->vas()->sync($validated['vas']);
            } else {
                $project->vas()->detach();
            }
        }

        return redirect()->route('projects.show', $project)->with('success', 'Project updated successfully.');
    }

    // Delete project
    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }
}
