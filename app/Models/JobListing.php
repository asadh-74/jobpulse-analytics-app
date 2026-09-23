<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class JobListing extends Model
{
    protected $fillable = [
        'remotive_id',
        'title',
        'company_name',
        'company_logo_url',
        'category',
        'job_type',
        'candidate_location',
        'salary_text',
        'tags',
        'description',
        'apply_url',
        'published_at',
        'first_seen_at',
        'is_active',
    ];

    protected $casts = [
        'tags' => 'array',
        'published_at' => 'datetime',
        'first_seen_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopePublishedBetween(Builder $query, $from, $to): Builder
    {
        return $query->whereBetween('published_at', [$from, $to]);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('company_name', 'like', "%{$term}%");
        });
    }
}
