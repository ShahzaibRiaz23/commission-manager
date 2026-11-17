<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
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

    public static function assignmentRoles(): array
    {
        return [
            'sales officer' => ['Sales Officer'],
            'expert' => ['VA'],
        ];
    }

    #[DataProvider('assignmentRoles')]
    public function test_project_list_contains_only_projects_assigned_to_the_current_user(string $roleName): void
    {
        $role = Role::create(['name' => $roleName, 'guard_name' => 'web']);
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $user->assignRole($role);
        $anotherUser->assignRole($role);
        $client = Client::create(['name' => 'Test Client']);

        $assignedProject = $this->createProjectForUser($client, $user, $roleName, 'Assigned Project');
        $this->createProjectForUser($client, $anotherUser, $roleName, 'Unassigned Project');

        $response = $this->actingAs($user)->get(route('projects.index'));

        $response->assertOk()
            ->assertSeeText($assignedProject->name)
            ->assertDontSeeText('Unassigned Project');
    }

    #[DataProvider('assignmentRoles')]
    public function test_project_details_return_404_for_projects_not_assigned_to_the_current_user(string $roleName): void
    {
        $role = Role::create(['name' => $roleName, 'guard_name' => 'web']);
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $user->assignRole($role);
        $anotherUser->assignRole($role);
        $client = Client::create(['name' => 'Test Client']);
        $project = $this->createProjectForUser($client, $anotherUser, $roleName, 'Unassigned Project');

        $response = $this->actingAs($user)->get(route('projects.show', $project));

        $response->assertNotFound();
    }

    #[DataProvider('assignmentRoles')]
    public function test_assigned_non_admin_project_details_hide_the_record_payment_action(string $roleName): void
    {
        $role = Role::create(['name' => $roleName, 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole($role);
        $client = Client::create(['name' => 'Test Client']);
        $project = $this->createProjectForUser($client, $user, $roleName, 'Assigned Project');

        $response = $this->actingAs($user)->get(route('projects.show', $project));

        $response->assertOk()
            ->assertSeeText($project->name)
            ->assertDontSeeText('+ Record Payment');
    }

    public function test_sales_officer_cannot_open_an_unassigned_project_edit_form(): void
    {
        $salesOfficerRole = Role::create(['name' => 'Sales Officer', 'guard_name' => 'web']);
        $salesOfficer = User::factory()->create();
        $anotherOfficer = User::factory()->create();
        $salesOfficer->assignRole($salesOfficerRole);
        $anotherOfficer->assignRole($salesOfficerRole);
        $client = Client::create(['name' => 'Test Client']);
        $project = $this->createProjectForUser($client, $anotherOfficer, 'Sales Officer', 'Unassigned Project');

        $response = $this->actingAs($salesOfficer)->get(route('projects.edit', $project));

        $response->assertNotFound();
    }

    public function test_sales_officer_cannot_update_an_unassigned_project(): void
    {
        $salesOfficerRole = Role::create(['name' => 'Sales Officer', 'guard_name' => 'web']);
        $salesOfficer = User::factory()->create();
        $anotherOfficer = User::factory()->create();
        $salesOfficer->assignRole($salesOfficerRole);
        $anotherOfficer->assignRole($salesOfficerRole);
        $client = Client::create(['name' => 'Test Client']);
        $project = $this->createProjectForUser($client, $anotherOfficer, 'Sales Officer', 'Unassigned Project');

        $response = $this->actingAs($salesOfficer)->put(route('projects.update', $project), [
            'name' => 'Changed Project',
            'client_id' => $client->id,
            'sales_officer_id' => $anotherOfficer->id,
            'project_amount' => 1000,
            'payment_type' => 'one-time',
            'status' => 'active',
        ]);

        $response->assertNotFound();
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Unassigned Project',
        ]);
    }

    public function test_sales_officer_cannot_delete_an_unassigned_project(): void
    {
        $salesOfficerRole = Role::create(['name' => 'Sales Officer', 'guard_name' => 'web']);
        $salesOfficer = User::factory()->create();
        $anotherOfficer = User::factory()->create();
        $salesOfficer->assignRole($salesOfficerRole);
        $anotherOfficer->assignRole($salesOfficerRole);
        $client = Client::create(['name' => 'Test Client']);
        $project = $this->createProjectForUser($client, $anotherOfficer, 'Sales Officer', 'Unassigned Project');

        $response = $this->actingAs($salesOfficer)->delete(route('projects.destroy', $project));

        $response->assertNotFound();
        $this->assertModelExists($project);
    }

    private function createProjectForUser(Client $client, User $user, string $roleName, string $name): Project
    {
        $project = Project::create([
            'name' => $name,
            'client_id' => $client->id,
            'sales_officer_id' => $roleName === 'Sales Officer' ? $user->id : null,
            'project_amount' => 1000,
            'payment_type' => 'one-time',
            'status' => 'active',
        ]);

        if ($roleName === 'VA') {
            $project->vas()->attach($user);
        }

        return $project;
    }
}
