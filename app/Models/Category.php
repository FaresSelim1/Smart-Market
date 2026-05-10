<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    // Allows the seeder to insert these fields
    protected $fillable = ['name', 'slug'];

    /**
     * Relationship: A category has many products
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}