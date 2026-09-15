<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ServiceListing;
use App\Models\User;
use Tests\TestCase;

class CatalogControllerTest extends TestCase
{
    public function test_public_catalog_shows_active_services_and_categories(): void
    {
        $listing = ServiceListing::factory()->create();
        $this->get('/')->assertSee($listing->title)->assertSee('Cleaning');
    }

    public function test_search_combines_text_category_and_city(): void
    {
        $category = Category::factory()->create(['name' => 'Plumbing', 'slug' => 'plumbing']);
        $match = ServiceListing::factory()->create(['title' => 'Kitchen tap repair', 'category_id' => $category->id]);
        ServiceListing::factory()->create(['title' => 'Kitchen cleaner', 'city' => 'Galle']);
        $this->get('/?q=Kitchen&category=plumbing&city=Colombo')->assertSee($match->title)->assertDontSee('Kitchen cleaner');
    }

    public function test_inactive_and_suspended_provider_services_are_hidden(): void
    {
        ServiceListing::factory()->create(['title' => 'Paused service', 'is_active' => false]);
        ServiceListing::factory()->create(['title' => 'Suspended provider service', 'provider_id' => User::factory()->create(['role' => 'provider', 'is_active' => false])->id]);
        $this->get('/')->assertDontSee('Paused service')->assertDontSee('Suspended provider service');
    }

    public function test_public_can_view_service_and_provider_details(): void
    {
        $listing = ServiceListing::factory()->create();
        $this->get('/services/'.$listing->slug)->assertSee($listing->description)->assertSee($listing->provider->name)->assertSee('Log in to book');
    }

    public function test_hidden_service_direct_link_is_not_found(): void
    {
        $listing = ServiceListing::factory()->create(['is_active' => false]);
        $this->get('/services/'.$listing->slug)->assertNotFound();
    }

    public function test_empty_search_has_a_recovery_action(): void
    {
        $this->get('/?q=nonexistent')->assertSee('No services found just yet')->assertSee('Explore all services');
    }

    public function test_sort_parameter_cannot_inject_sql(): void
    {
        $this->get('/?sort=price;DROP%20TABLE%20users')->assertSessionHasErrors('sort');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_price_sort_orders_matching_services(): void
    {
        ServiceListing::factory()->create(['title' => 'Expensive job', 'price' => 9000]);
        ServiceListing::factory()->create(['title' => 'Affordable job', 'price' => 1500]);
        $this->get('/?sort=price_low')->assertSeeInOrder(['Affordable job', 'Expensive job']);
    }

    public function test_html_in_provider_and_service_text_is_escaped(): void
    {
        $listing = ServiceListing::factory()->create(['title' => '<script>alert(1)</script>', 'description' => '<script>alert(2)</script>']);
        $listing->provider->update(['bio' => '<script>alert(3)</script>', 'name' => '<script>alert(4)</script>']);
        $response = $this->get('/services/'.$listing->slug);
        foreach ([1, 2, 3, 4] as $i) {
            $response->assertSee('&lt;script&gt;alert('.$i.')&lt;/script&gt;', false)->assertDontSee('<script>alert('.$i.')</script>', false);
        }
    }
}
