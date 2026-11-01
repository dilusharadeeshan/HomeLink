<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function listings(): HasMany
    {
        return $this->hasMany(ServiceListing::class);
    }

    public function imageUrl(): string
    {
        $image = 'images/'.$this->slug.'.jpg';

        return asset(file_exists(public_path($image)) ? $image : 'images/home.jpg');
    }
}
