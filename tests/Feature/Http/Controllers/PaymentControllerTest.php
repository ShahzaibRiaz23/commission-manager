<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use App\Services\CommissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_with_sales_officer_role_sees_new_project_payment_in_ledger(): void
    {
        $superAdmin = User::factory()->create();
        $assignedOfficer = User::factory()->create();
        $salesOfficerRole = Role::create(['name' => 'Sales Officer', 'guard_name' => 'web']);

        $superAdmin->assignRole(
            Role::create(['name' => 'Super Admin', 'guard_name' => 'web']),
            $salesOfficerRole,
        );
        $assignedOfficer->assignRole($salesOfficerRole);

        $client = Client::create(['name' => 'Test Client']);
        $project = Project::create([
            'name' => 'Ledger Entry Project',
            'client_id' => $client->id,
            'sales_officer_id' => $assignedOfficer->id,
            'project_amount' => 1000,
            'payment_type' => 'one-time',
            'status' => 'active',
        ]);

        $response = $this->actingAs($superAdmin)->post(route('payments.store', $project), [
            'payment_amount' => 250,
            'payment_date' => '2026-11-17',
            'payment_type' => 'one-time',
            'status' => 'pending',
        ]);

        $response->assertRedirect(route('projects.show', $project));
        $this->assertDatabaseHas('project_payments', [
            'project_id' => $project->id,
            'payment_amount' => 250,
            'status' => 'pending',
        ]);

        $ledgerResponse = $this->get(route('payments.index'));

        $ledgerResponse->assertOk()
            ->assertSeeText('Ledger Entry Project')
            ->assertSeeText('$250.00');
    }

    public function test_sales_officer_ledger_shows_only_payments_for_assigned_projects(): void
    {
        $salesOfficer = User::factory()->create();
        $anotherOfficer = User::factory()->create();
        $salesOfficerRole = Role::create(['name' => 'Sales Officer', 'guard_name' => 'web']);

        $salesOfficer->assignRole($salesOfficerRole);
        $anotherOfficer->assignRole($salesOfficerRole);

        $client = Client::create(['name' => 'Test Client']);
        $assignedProject = Project::create([
            'name' => 'Assigned Ledger Project',
            'client_id' => $client->id,
            'sales_officer_id' => $salesOfficer->id,
            'project_amount' => 1000,
            'payment_type' => 'one-time',
            'status' => 'active',
        ]);
        $anotherProject = Project::create([
            'name' => 'Other Officer Ledger Project',
            'client_id' => $client->id,
            'sales_officer_id' => $anotherOfficer->id,
            'project_amount' => 1000,
            'payment_type' => 'one-time',
            'status' => 'active',
        ]);

        $commissionService = app(CommissionService::class);
        foreach ([$assignedProject, $anotherProject] as $project) {
            $commissionService->recordPayment($project, [
                'payment_amount' => 250,
                'payment_date' => '2026-11-17',
                'payment_type' => 'one-time',
            ]);
        }

        $response = $this->actingAs($salesOfficer)->get(route('payments.index'));

        $response->assertOk()
            ->assertSeeText('Assigned Ledger Project')
            ->assertDontSeeText('Other Officer Ledger Project');
    }
}
