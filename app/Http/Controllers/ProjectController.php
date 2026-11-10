<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // List all projects with pagination and relationships
    public function index()
    {
        $projects = Project::with(['client', 'salesOfficer', 'vas'])->latest()->paginate(10);
        return view('projects.index', compact('projects'));
    }

    // Show form to create a project
    public function create()
    {
        $clients = Client::all();
        // Fetch users who are Sales Officers or VAs (or all users except admin)
        $users = User::all();
        
        return view('projects.create', compact('clients', 'users'));
    }

    // Store a new project
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'client_id' => 'required|exists:clients,id',
            'sales_officer_id' => 'nullable|exists:users,id',
            'project_amount' => 'required|numeric|min:0',
            'payment_type' => 'required|in:one-time,recurring',
            'va_commission_percentage' => 'required|numeric|min:0|max:100',
            'sales_commission_percentage' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:pending,active,on_hold,completed,cancelled',
            'start_date' => 'nullable|date',
            'deadline' => 'nullable|date',
            'vas' => 'nullable|array',
            'vas.*' => 'exists:users,id',
        ]);

        // Create the project
        $project = Project::create($validated);

        // Attach VAs if selected
        if (!empty($validated['vas'])) {
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
        $clients = Client::all();
        $users = User::all();
        $project->load('vas');
        
        return view('projects.edit', compact('project', 'clients', 'users'));
    }

    // Update project
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'client_id' => 'required|exists:clients,id',
            'sales_officer_id' => 'nullable|exists:users,id',
            'project_amount' => 'required|numeric|min:0',
            'payment_type' => 'required|in:one-time,recurring',
            'va_commission_percentage' => 'required|numeric|min:0|max:100',
            'sales_commission_percentage' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:pending,active,on_hold,completed,cancelled',
            'start_date' => 'nullable|date',
            'deadline' => 'nullable|date',
            'vas' => 'nullable|array',
            'vas.*' => 'exists:users,id',
        ]);

        $project->update($validated);

        // Sync VAs pivot table
        if (isset($validated['vas'])) {
            $project->vas()->sync($validated['vas']);
        } else {
            $project->vas()->detach();
        }

        return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
    }

    // Delete project
    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }
}