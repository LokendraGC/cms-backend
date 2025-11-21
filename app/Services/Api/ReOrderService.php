<?php

namespace App\Services\Api;

use App\Repositories\ReOrderRepository;

class CategoryService
{
    protected $reOrderRepo;

    public function __construct(ReOrderRepository $reOrderRepo)
    {
        $this->reOrderRepo = $reOrderRepo;
    }

    /**
     * Reorder categories
     */
    public function reorderCategories(array $categoryIds)
    {
        try {
            return $this->reOrderRepo->reorderItems($categoryIds, 'App\Models\Category');
        } catch (\Exception $e) {
            throw new \Exception('Failed to reorder categories: ' . $e->getMessage());
        }
    }

    
}
