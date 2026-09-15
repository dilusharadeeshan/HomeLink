<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Category;
use App\Models\ServiceListing;
use App\Models\User;
use Tests\TestCase;

class ProviderControllerTest extends TestCase
{
    private function payload(Category $category, array $overrides = []): array
    {
        return array_replace(['title' => 'A new service', 'description' => 'A detailed service description with all the work included.', 'category_id' => $category->id, 'city' => 'Colombo', 'price' => 3500, 'is_active' => '1'], $overrides);
    }

    public function test_provider_can_create_an_owned_service(): void
    {
        $provider = User::factory()->create(['role' => 'provider']);
        $category = Category::factory()->create();
        $this->actingAs($provider)->post('/provider/services', $this->payload($category, ['provider_id' => 999]))->assertRedirectToRoute('provider.services');
        $this->assertDatabaseHas('service_listings', ['title' => 'A new service', 'provider_id' => $provider->id, 'price' => 3500]);
    }

    public function test_provider_can_edit_and_pause_their_service(): void
    {
        $listing = ServiceListing::factory()->create();
        $this->actingAs($listing->provider)->put('/provider/services/'.$listing->id, $this->payload($listing->category, ['is_active' => '0', 'title' => 'Updated service']))->assertRedirectToRoute('provider.services');
        $this->assertDatabaseHas('service_listings', ['id' => $listing->id, 'title' => 'Updated service', 'is_active' => false]);
    }

    public function test_other_provider_cannot_modify_or_delete_service(): void
    {
        $listing = ServiceListing::factory()->create();
        $other = User::factory()->create(['role' => 'provider']);
        $this->actingAs($other)->put('/provider/services/'.$listing->id, $this->payload($listing->category))->assertForbidden();
        $this->delete('/provider/services/'.$listing->id)->assertForbidden();
        $this->assertDatabaseHas('service_listings', ['id' => $listing->id, 'deleted_at' => null, 'title' => $listing->title]);
    }

    public function test_removing_service_preserves_booking_history(): void
    {
        $booking = Booking::factory()->create();
        $listing = $booking->serviceListing;
        $this->actingAs($listing->provider)->delete('/provider/services/'.$listing->id)->assertRedirectToRoute('provider.services');
        $this->assertSoftDeleted($listing);
        $this->assertModelExists($booking);
        $this->get('/bookings/'.$booking->id)->assertSee($booking->service_title);
    }

    public function test_customer_cannot_create_service(): void
    {
        $this->actingAs(User::factory()->create())->post('/provider/services', [])->assertForbidden();
        $this->assertDatabaseCount('service_listings', 0);
    }

    public function test_invalid_price_and_description_are_rejected(): void
    {
        $provider = User::factory()->create(['role' => 'provider']);
        $category = Category::factory()->create();
        $this->actingAs($provider)->post('/provider/services', $this->payload($category, ['price' => -10, 'description' => 'tiny']))->assertSessionHasErrors(['price', 'description']);
        $this->assertDatabaseCount('service_listings',0);
    }
}
