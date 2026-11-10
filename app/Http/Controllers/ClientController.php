<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    // List all clients
    public function index()
    {
        $clients = Client::latest()->paginate(10);
        return view('clients.index', compact('clients'));
    }

    // Show form to create a client
    public function create()
    {
        return view('clients.create');
    }

    // Store a new client
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
        ]);

        Client::create($validated);

        return redirect()->route('clients.index')->with('success', 'Client created successfully.');
    }

    // Show client details
    public function show(Client $client)
    {
        return view('clients.show', compact('client'));
    }

    // Show form to edit client
    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    // Update client
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
        ]);

        $client->update($validated);

        return redirect()->route('clients.index')->with('success', 'Client updated successfully.');
    }

    // Delete client
    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Client deleted successfully.');
    }
}