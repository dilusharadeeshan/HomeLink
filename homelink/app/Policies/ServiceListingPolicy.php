<?php

namespace App\Policies;

use App\Models\ServiceListing;
use App\Models\User;

class ServiceListingPolicy
{
    public function update(User $user, ServiceListing $listing): bool
    {
        return $user->role === 'provider' && $listing->provider_id === $user->id;
    }

    public function delete(User $user, ServiceListing $listing): bool
    {
        return $this->update($user, $listing);
    }
}
