<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait SlugGenerateTrait
{
    /**
     * Create slug for new model
     */
    public function createSlug($name, $slug, $model)
    {
        // If slug is empty, generate from name
        if (empty($slug)) {
            $slug = Str::slug($name);
        } else {
            // Convert provided slug to URL-friendly format
            $slug = Str::slug($slug);
        }

        // Always check for uniqueness and generate unique slug if needed
        return $this->makeSlugUnique($slug, $model);
    }

    /**
     * Update slug for existing model
     */
    public function getSlug($payload, $name, $slug)
    {
        // If slug is empty, generate from name
        if (empty($slug)) {
            $slug = Str::slug($name);
        } else {
            // Convert provided slug to URL-friendly format
            $slug = Str::slug($slug);
        }

        // If slug hasn't changed, return the original
        if ($slug === $payload->slug) {
            return $slug;
        }

        // Check for uniqueness (excluding current model)
        return $this->makeSlugUnique($slug, $payload, $payload->id);
    }

    /**
     * Make slug unique across the table
     */
    private function makeSlugUnique($slug, $model, $excludeId = null)
    {
        $originalSlug = $slug;
        $counter = 1;

        // Check if slug exists (excluding current model for updates)
        $query = $model->newQuery()->where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->exists()) {
            $slug = $originalSlug . '-' . $counter;

            // Update query for next iteration
            $query = $model->newQuery()->where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }

            $counter++;
        }

        return $slug;
    }

    /**
     * Alternative simplified version using Laravel's built-in functionality
     */
    public function generateUniqueSlug($model, $name, $slug = null, $excludeId = null)
    {
        // Generate slug from name if not provided
        $slug = $slug ? Str::slug($slug) : Str::slug($name);

        $query = $model->newQuery()->where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        // If slug doesn't exist, return it
        if (!$query->exists()) {
            return $slug;
        }

        // Generate unique slug by appending numbers
        $counter = 1;
        $originalSlug = $slug;

        do {
            $slug = $originalSlug . '-' . $counter;
            $query = $model->newQuery()->where('slug', $slug);

            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }

            $counter++;
        } while ($query->exists());

        return $slug;
    }
}
