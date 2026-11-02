<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $attributes = ['role' => 'customer', 'is_active' => true, 'city' => 'Colombo'];

    protected $fillable = ['name', 'email', 'password', 'role', 'phone', 'city', 'bio', 'profile_photo_path', 'is_active'];

    protected $hidden = ['password', 'remember_token'];

    protected static function booted(): void
    {
        static::saving(function (self $user): void {
            $user->email = mb_strtolower(trim($user->email ?? ''));
        });
    }

    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed', 'is_active' => 'boolean'];
    }

    public function listings(): HasMany
    {
        return $this->hasMany(ServiceListing::class, 'provider_id');
    }

    public function initials(): string
    {
        return collect(explode(' ', $this->name))->take(2)->map(fn ($part) => mb_substr($part, 0, 1))->join('');
    }

    public function profilePhotoUrl(): ?string
    {
        return $this->profile_photo_path ? Storage::disk('public')->url($this->profile_photo_path) : null;
    }
}
