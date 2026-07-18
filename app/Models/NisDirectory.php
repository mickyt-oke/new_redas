<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NisDirectory extends Model
{
    /** @use HasFactory<\Database\Factories\NisDirectoryFactory> */
    use HasFactory;

    protected $fillable = [
        'category',
        'name',
        'state',
        'country',
        'city',
        'region',
        'type',
        'address',
        'email',
        'code',
        'sort_order',
    ];

    /**
     * Scope a query to filter by category.
     */
    public function scopeCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to search across relevant text columns.
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (blank($search)) {
            return $query;
        }

        $term = '%' . str_replace(' ', '%', trim($search)) . '%';

        return $query->where(function (Builder $q) use ($term) {
            $q->where('name', 'like', $term)
              ->orWhere('state', 'like', $term)
              ->orWhere('country', 'like', $term)
              ->orWhere('city', 'like', $term)
              ->orWhere('region', 'like', $term)
              ->orWhere('type', 'like', $term)
              ->orWhere('address', 'like', $term);
        });
    }
}
