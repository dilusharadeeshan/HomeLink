<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class HomeLinkTest extends TestCase
{
    public static function dashboards(): array
    {
        return [['customer', 'Your home'], ['provider', 'Your services']];
    }

    #[DataProvider('dashboards')]
    public function test_role_dashboard_displays_relevant_workspace(string $role, string $text): void
    {
        $this->actingAs(User::factory()->create(['role' => $role]))->get('/dashboard')->assertSee($text);
    }

    public function test_admin_dashboard_redirects_to_administration(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))->get('/dashboard')->assertRedirectToRoute('admin.index');
    }

    public function test_provider_can_finish_an_accepted_job(): void
    {
        $booking = Booking::factory()->create(['status' => 'accepted']);
        $this->actingAs($booking->provider)->patch('/bookings/'.$booking->id.'/status', ['status' => 'in_progress'])->assertSessionHasNoErrors();
        $this->patch('/bookings/'.$booking->id.'/status', ['status' => 'completed'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'completed']);
        $this->assertDatabaseCount('booking_events', 2);
    }

    public function test_guest_profile_redirects_to_login(): void
    {
        $this->get('/profile')->assertRedirectToRoute('login');
    }

    public function test_security_headers_are_sent(): void
    {
        $this->get('/')->assertHeader('X-Content-Type-Options', 'nosniff')->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    }
}
