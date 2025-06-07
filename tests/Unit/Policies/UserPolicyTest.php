<?php

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected UserPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new UserPolicy;
    }

    public function test_admin_can_do_everything()
    {
        $admin = User::factory()->admin()->create();
        $other = User::factory()->attendee()->create();

        $this->assertTrue($this->policy->viewAny($admin));
        $this->assertTrue($this->policy->view($admin, $other));
        $this->assertTrue($this->policy->create($admin));
        $this->assertTrue($this->policy->update($admin, $other));
        $this->assertTrue($this->policy->delete($admin, $other));
    }

    public function test_non_admin_can_only_manage_self()
    {
        $user = User::factory()->organizer()->create();
        $other = User::factory()->attendee()->create();

        $this->assertFalse($this->policy->viewAny($user));
        $this->assertTrue($this->policy->view($user, $user));
        $this->assertFalse($this->policy->view($user, $other));
        $this->assertFalse($this->policy->create($user));
        $this->assertTrue($this->policy->update($user, $user));
        $this->assertFalse($this->policy->update($user, $other));
        $this->assertTrue($this->policy->delete($user, $user));
        $this->assertFalse($this->policy->delete($user, $other));
    }
}
