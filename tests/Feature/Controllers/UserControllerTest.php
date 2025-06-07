<?php

namespace Tests\Feature\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticateAs(User $user)
    {
        $this->actingAs($user, 'sanctum');
    }

    public function test_admin_can_perform_all_actions()
    {
        $admin = User::factory()->admin()->create();
        $this->authenticateAs($admin);

        $user = User::factory()->attendee()->create();
        // dd($admin, 'test');
        $this->getJson('/api/users')->assertOk();

        $this->getJson("/api/users/{$user->id}")->assertOk();

        $newUserData = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => UserRole::Organizer->value,
        ];
        $this->postJson('/api/users', $newUserData)->assertCreated();

        $updateData = [
            'name' => 'Updated Name',
            'role' => UserRole::Admin->value,
        ];
        $this->patchJson("/api/users/{$user->id}", $updateData)->assertOk()
            ->assertJsonFragment(['name' => 'Updated Name', 'role' => UserRole::Admin->value]);

        $this->deleteJson("/api/users/{$user->id}")->assertOk();
    }

    public function test_organizer_can_only_view_edit_delete_self()
    {
        $organizer = User::factory()->organizer()->create();
        $other = User::factory()->attendee()->create();
        $this->authenticateAs($organizer);

        $this->getJson('/api/users')->assertForbidden();

        $this->getJson("/api/users/{$organizer->id}")->assertOk();

        $this->getJson("/api/users/{$other->id}")->assertForbidden();

        $updateData = ['name' => 'Self Updated'];
        $this->patchJson("/api/users/{$organizer->id}", $updateData)->assertOk()
            ->assertJsonFragment(['name' => 'Self Updated']);

        $this->patchJson("/api/users/{$other->id}", ['name' => 'Should Fail'])
            ->assertForbidden();

        $this->deleteJson("/api/users/{$organizer->id}")->assertOk();

        $this->deleteJson("/api/users/{$other->id}")->assertForbidden();

        $newUserData = [
            'name' => 'New User',
            'email' => 'newuser2@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => UserRole::Attendee->value,
        ];
        $this->postJson('/api/users', $newUserData)->assertForbidden();
    }

    public function test_attendee_can_only_view_edit_delete_self()
    {
        $attendee = User::factory()->attendee()->create();
        $other = User::factory()->organizer()->create();
        $this->authenticateAs($attendee);

        $this->getJson('/api/users')->assertForbidden();

        $this->getJson("/api/users/{$attendee->id}")->assertOk();

        $this->getJson("/api/users/{$other->id}")->assertForbidden();

        $updateData = ['name' => 'Self Updated'];
        $this->patchJson("/api/users/{$attendee->id}", $updateData)->assertOk()
            ->assertJsonFragment(['name' => 'Self Updated']);

        $this->patchJson("/api/users/{$other->id}", ['name' => 'Should Fail'])
            ->assertForbidden();

        $this->deleteJson("/api/users/{$attendee->id}")->assertOk();

        $this->deleteJson("/api/users/{$other->id}")->assertForbidden();

        $newUserData = [
            'name' => 'New User',
            'email' => 'newuser3@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => UserRole::Organizer->value,
        ];
        $this->postJson('/api/users', $newUserData)->assertForbidden();
    }

    public function test_only_admin_can_change_role_on_update()
    {
        $admin = User::factory()->admin()->create();
        $organizer = User::factory()->organizer()->create();
        $attendee = User::factory()->attendee()->create();

        $this->authenticateAs($admin);
        $this->patchJson("/api/users/{$organizer->id}", ['role' => UserRole::Admin->value])
            ->assertOk()
            ->assertJsonFragment(['role' => UserRole::Admin->value]);

        $this->authenticateAs($organizer);
        $this->patchJson("/api/users/{$organizer->id}", ['role' => UserRole::Admin->value])
            ->assertUnprocessable();

        $this->authenticateAs($attendee);
        $this->patchJson("/api/users/{$attendee->id}", ['role' => UserRole::Admin->value])
            ->assertUnprocessable();
    }

    public function test_unauthenticated_users_cannot_access_any_action()
    {
        $user = User::factory()->create();

        $this->getJson('/api/users')->assertUnauthorized();
        $this->getJson("/api/users/{$user->id}")->assertUnauthorized();
        $this->postJson('/api/users', [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => UserRole::Attendee->value,
        ])->assertUnauthorized();
        $this->patchJson("/api/users/{$user->id}", ['name' => 'Test'])->assertUnauthorized();
        $this->deleteJson("/api/users/{$user->id}")->assertUnauthorized();
    }
}
