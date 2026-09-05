<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    private function registration(array $overrides = []): array
    {
        return array_replace(['name' => 'Sam Test', 'email' => 'sam@example.test', 'password' => 'HomeLink@123', 'password_confirmation' => 'HomeLink@123', 'role' => 'customer', 'phone' => '0771234567', 'city' => 'Colombo'], $overrides);
    }

    public function test_customer_registration_creates_a_hashed_account(): void
    {
        $this->post('/register', $this->registration())->assertRedirectToRoute('dashboard');
        $user = User::where('email', 'sam@example.test')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertTrue(Hash::check('HomeLink@123', $user->password));
        $this->assertSame('customer', $user->role);
    }

    public function test_provider_can_register(): void
    {
        $this->post('/register', $this->registration(['role' => 'provider']))->assertRedirectToRoute('dashboard');
        $this->assertDatabaseHas('users', ['email' => 'sam@example.test', 'role' => 'provider']);
    }

    public static function invalidRegistration(): array
    {
        return ['admin role' => [['role' => 'admin'], 'role'], 'weak password' => [['password' => 'short', 'password_confirmation' => 'short'], 'password'], 'mismatch' => [['password_confirmation' => 'Other@1234'], 'password'], 'invalid email' => [['email' => 'bad'], 'email'], 'invalid phone' => [['phone' => 'hello'], 'phone'], 'missing city' => [['city' => ''], 'city']];
    }

    #[DataProvider('invalidRegistration')]
    public function test_invalid_registration_is_rejected(array $values, string $field): void
    {
        $this->post('/register', $this->registration($values))->assertSessionHasErrors($field);
        $this->assertDatabaseCount('users', 0);
    }

    public function test_duplicate_email_is_rejected(): void
    {
        User::factory()->create(['email' => 'sam@example.test']);
        $this->post('/register', $this->registration())->assertSessionHasErrors('email');
        $this->assertDatabaseCount('users', 1);
    }

    public function test_duplicate_email_is_rejected_case_insensitively(): void
    {
        User::factory()->create(['email' => 'Sam@Example.Test']);
        $this->post('/register', $this->registration(['email' => 'sam@example.test']))->assertSessionHasErrors('email');
        $this->assertDatabaseCount('users', 1);
    }

    public function test_active_user_can_log_in(): void
    {
        $user = User::factory()->create(['password' => 'HomeLink@123']);
        $this->post('/login', ['email' => $user->email, 'password' => 'HomeLink@123'])->assertRedirectToRoute('dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_accepts_mixed_case_email(): void
    {
        $user = User::factory()->create(['email' => 'sam@example.test', 'password' => 'HomeLink@123']);
        $this->post('/login', ['email' => 'SAM@EXAMPLE.TEST', 'password' => 'HomeLink@123'])->assertRedirectToRoute('dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_incorrect_password_does_not_authenticate(): void
    {
        $user = User::factory()->create(['password' => 'HomeLink@123']);
        $this->post('/login', ['email' => $user->email, 'password' => 'incorrect'])->assertSessionHasErrors(['email' => 'These credentials do not match an active account.']);
        $this->assertGuest();
    }

    public function test_suspended_user_cannot_log_in(): void
    {
        $user = User::factory()->create(['is_active' => false, 'password' => 'HomeLink@123']);
        $this->post('/login', ['email' => $user->email, 'password' => 'HomeLink@123'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_suspended_existing_session_is_revoked(): void
    {
        $user = User::factory()->create(['is_active' => false]);
        $this->actingAs($user)->get('/dashboard')->assertRedirectToRoute('login');
        $this->assertGuest();
    }

    public function test_profile_updates_ignore_role_and_active_flag_injection(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->put('/profile', ['name' => 'New Name', 'email' => $user->email, 'phone' => '0771234567', 'city' => 'Galle', 'bio' => 'A useful introduction', 'role' => 'admin', 'is_active' => false])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'New Name', 'city' => 'Galle', 'role' => 'customer', 'is_active' => true]);
    }

    public function test_password_change_requires_current_password(): void
    {
        $user = User::factory()->create(['password' => 'HomeLink@123']);
        $this->actingAs($user)->put('/profile/password', ['current_password' => 'wrong', 'password' => 'NewPassword123', 'password_confirmation' => 'NewPassword123'])->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('HomeLink@123', $user->fresh()->password));
    }

    public function test_password_can_be_changed_with_current_password(): void
    {
        $user = User::factory()->create(['password' => 'HomeLink@123']);
        $this->actingAs($user)->put('/profile/password', ['current_password' => 'HomeLink@123', 'password' => 'NewPassword123', 'password_confirmation' => 'NewPassword123'])->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('NewPassword123', $user->fresh()->password));
    }

    public function test_logout_ends_the_authenticated_session(): void
    {
        $this->actingAs(User::factory()->create())->post('/logout')->assertRedirectToRoute('home');
        $this->assertGuest();
    }

    public function test_login_is_throttled_after_six_failed_attempts(): void
    {
        for ($i = 0; $i < 6; $i++) {
            $this->post('/login', ['email' => 'no@example.test', 'password' => 'bad']);
        }
        $this->post('/login', ['email' => 'no@example.test', 'password' => 'bad'])->assertTooManyRequests();
    }

    public function test_missing_csrf_token_is_rejected_outside_testing_bypass(): void
    {
        $this->app['env'] = 'local';
        $this->post('/register', $this->registration())->assertStatus(419);
        $this->assertDatabaseCount('users',0);
    }
}
