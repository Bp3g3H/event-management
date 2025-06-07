<?php

namespace Tests\Feature\FilterAndSort;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserScopeFilterAndSortTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        User::factory()->admin()->create([
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'created_at' => now()->subDays(3),
        ]);
        User::factory()->organizer()->create([
            'name' => 'Bob',
            'email' => 'bob@example.com',
            'created_at' => now()->subDays(2),
        ]);
        User::factory()->attendee()->create([
            'name' => 'Charlie',
            'email' => 'charlie@example.com',
            'created_at' => now()->subDay(),
        ]);
    }

    public function test_filter_by_name()
    {
        $users = User::query()->filterAndSort(['name' => 'Ali'])->get();
        $this->assertCount(1, $users);
        $this->assertEquals('Alice', $users->first()->name);
    }

    public function test_filter_by_email()
    {
        $users = User::query()->filterAndSort(['email' => 'bob@'])->get();
        $this->assertCount(1, $users);
        $this->assertEquals('Bob', $users->first()->name);
    }

    public function test_filter_by_role()
    {
        $users = User::query()->filterAndSort(['role' => UserRole::Attendee->value])->get();
        $this->assertCount(1, $users);
        $this->assertEquals('Charlie', $users->first()->name);
    }

    public function test_sort_by_name_asc()
    {
        $users = User::query()->filterAndSort(['sort_by' => 'name', 'sort_order' => 'asc'])->pluck('name')->toArray();
        $this->assertEquals(['Alice', 'Bob', 'Charlie'], $users);
    }

    public function test_sort_by_name_desc()
    {
        $users = User::query()->filterAndSort(['sort_by' => 'name', 'sort_order' => 'desc'])->pluck('name')->toArray();
        $this->assertEquals(['Charlie', 'Bob', 'Alice'], $users);
    }

    public function test_sort_by_created_at_asc()
    {
        $users = User::query()->filterAndSort(['sort_by' => 'created_at', 'sort_order' => 'asc'])->pluck('name')->toArray();
        $this->assertEquals(['Alice', 'Bob', 'Charlie'], $users);
    }

    public function test_sort_by_created_at_desc()
    {
        $users = User::query()->filterAndSort(['sort_by' => 'created_at', 'sort_order' => 'desc'])->pluck('name')->toArray();
        $this->assertEquals(['Charlie', 'Bob', 'Alice'], $users);
    }

    public function test_default_sort_is_created_at_desc()
    {
        $users = User::query()->filterAndSort([])->pluck('name')->toArray();
        $this->assertEquals(['Charlie', 'Bob', 'Alice'], $users);
    }
}