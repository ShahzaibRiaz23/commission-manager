<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProjectControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_with_sales_officer_role_can_assign_project_to_selected_officer(): void
    {
        $superAdmin = User::factory()->create();
        $selectedOfficer = User::factory()->create();
        $anotherOfficer = User::factory()->create();
        $salesOfficerRole = Role::create(['name' => 'Sales Officer', 'guard_name' => 'web']);

        $superAdmin->assignRole(
            Role::create(['name' => 'Super Admin', 'guard_name' => 'web']),
            $salesOfficerRole,
        );
        $selectedOfficer->assignRole($salesOfficerRole);
        $anotherOfficer->assignRole($salesOfficerRole);

        $client = Client::create(['name' => 'Test Client']);
        $project = Project::create([
            'name' => 'Assigned Project',
            'client_id' => $client->id,
            'sales_officer_id' => $anotherOfficer->id,
            'project_amount' => 1000,
            'payment_type' => 'one-time',
            'status' => 'active',
        ]);

        $response = $this->actingAs($superAdmin)->put(route('projects.update', $project), [
            'name' => $project->name,
            'client_id' => $client->id,
            'sales_officer_id' => $selectedOfficer->id,
            'project_amount' => 1000,
            'payment_type' => 'one-time',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('projects.show', $project));
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'sales_officer_id' => $selectedOfficer->id,
        ]);
    }

    public function test_super_admin_with_sales_officer_role_can_assign_new_project_to_selected_officer(): void
    {
        $superAdmin = User::factory()->create();
        $selectedOfficer = User::factory()->create();
        $salesOfficerRole = Role::create(['name' => 'Sales Officer', 'guard_name' => 'web']);

        $superAdmin->assignRole(
            Role::create(['name' => 'Super Admin', 'guard_name' => 'web']),
            $salesOfficerRole,
        );
        $selectedOfficer->assignRole($salesOfficerRole);

        $client = Client::create(['name' => 'Test Client']);

        $response = $this->actingAs($superAdmin)->post(route('projects.store'), [
            'name' => 'New Assigned Project',
            'client_id' => $client->id,
            'sales_officer_id' => $selectedOfficer->id,
            'project_amount' => 1000,
            'payment_type' => 'one-time',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('projects.index'));
        $this->assertDatabaseHas('projects', [
            'name' => 'New Assigned Project',
            'sales_officer_id' => $selectedOfficer->id,
        ]);
    }

    public function test_sales_officer_created_project_is_assigned_to_their_account(): void
    {
        $salesOfficer = User::factory()->create();
        $salesOfficer->assignRole(Role::create(['name' => 'Sales Officer', 'guard_name' => 'web']));
        $client = Client::create(['name' => 'Test Client']);

        $response = $this->actingAs($salesOfficer)->post(route('projects.store'), [
            'name' => 'Officer Created Project',
            'client_id' => $client->id,
            'project_amount' => 1000,
            'payment_type' => 'one-time',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('projects.index'));
        $this->assertDatabaseHas('projects', [
            'name' => 'Officer Created Project',
            'sales_officer_id' => $salesOfficer->id,
        ]);
    }
}
