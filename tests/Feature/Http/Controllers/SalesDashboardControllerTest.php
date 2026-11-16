<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SalesDashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_officer_dashboard_lists_only_projects_assigned_to_that_officer(): void
    {
        $salesOfficer = User::factory()->create();
        $anotherOfficer = User::factory()->create();
        $salesOfficerRole = Role::create(['name' => 'Sales Officer', 'guard_name' => 'web']);

        $salesOfficer->assignRole($salesOfficerRole);
        $anotherOfficer->assignRole($salesOfficerRole);

        $client = Client::create(['name' => 'Test Client']);

        Project::create([
            'name' => 'Assigned Project',
            'client_id' => $client->id,
            'sales_officer_id' => $salesOfficer->id,
            'project_amount' => 1000,
            'payment_type' => 'one-time',
            'status' => 'active',
        ]);
        Project::create([
            'name' => 'Another Officer Project',
            'client_id' => $client->id,
            'sales_officer_id' => $anotherOfficer->id,
            'project_amount' => 1000,
            'payment_type' => 'one-time',
            'status' => 'active',
        ]);

        $response = $this->actingAs($salesOfficer)->get(route('sales.dashboard'));

        $response->assertOk()
            ->assertSeeText('Assigned Project')
            ->assertDontSeeText('Another Officer Project');
    }
}
