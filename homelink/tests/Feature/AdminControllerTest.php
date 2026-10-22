<?php

namespace Tests\Feature;

use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    public function test_admin_can_view_accounts_and_platform_stats(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create();
        $this->actingAs($admin)->get('/admin')->assertSee($customer->email)->assertSee('People on HomeLink');
    }

    public function test_admin_can_suspend_a_customer(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create();
        $this->actingAs($admin)->patch('/admin/users/'.$customer->id.'/status')->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', ['id' => $customer->id, 'is_active' => false]);
    }

    public function test_admin_can_reactivate_a_suspended_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['is_active' => false]);
        $this->actingAs($admin)->patch('/admin/users/'.$customer->id.'/status')->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', ['id' => $customer->id, 'is_active' => true]);
    }

    public function test_administrators_are_protected_from_suspension(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->patch('/admin/users/'.$admin->id.'/status')->assertForbidden();
        $this->assertDatabaseHas('users', ['id' => $admin->id, 'is_active' => true]);
    }

    

    #[DataProvider('ordinaryRoles')]
    public function test_ordinary_users_cannot_open_admin_tools(string $role): void
    {
        $this->actingAs(User::factory()->create(['role' => $role]))->get('/admin')->assertForbidden();
    }
}
