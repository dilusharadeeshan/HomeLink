<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('customer')->index();
            $table->string('phone', 30)->nullable();
            $table->string('city', 80)->default('Colombo');
            $table->text('bio')->nullable();
            $table->boolean('is_active')->default(true);
        });
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon');
            $table->string('color');
            $table->timestamps();
        });
        Schema::create('service_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('users');
            $table->foreignId('category_id')->constrained();
            $table->string('title', 120);
            $table->string('slug')->unique();
            $table->text('description');
            $table->unsignedInteger('price');
            $table->string('pricing_note', 160)->nullable();
            $table->string('city', 80)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users');
            $table->foreignId('provider_id')->constrained('users');
            $table->foreignId('service_listing_id')->constrained();
            $table->string('service_title');
            $table->unsignedInteger('quoted_price');
            $table->dateTime('starts_at')->index();
            $table->string('address', 255);
            $table->string('phone', 30);
            $table->text('notes')->nullable();
            $table->string('status')->default('pending')->index();
            $table->timestamps();
            $table->index(['provider_id', 'starts_at']);
        });
        Schema::create('booking_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('provider_id')->constrained('users');
            $table->dateTime('starts_at');
            $table->unique(['provider_id', 'starts_at']);
        });
        Schema::create('booking_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('actor_id')->constrained('users');
            $table->string('status');
            $table->string('note', 255);
            $table->timestamps();
        });
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->unique()->constrained();
            $table->foreignId('customer_id')->constrained('users');
            $table->unsignedTinyInteger('rating');
            $table->text('comment');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['reviews', 'booking_events', 'booking_slots', 'bookings', 'service_listings', 'categories'] as $name) {
            Schema::dropIfExists($name);
        }
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['role', 'phone', 'city', 'bio', 'is_active']));
    }
};
