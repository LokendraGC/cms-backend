<?php

namespace App\Services\Api;

use App\Enums\CategoryType;
use App\Repositories\CategoryRepository;
use App\Repositories\ReOrderRepository;

class CategoryService
{
    protected $repo;
    protected $reorder_repo;

    public function __construct(CategoryRepository $repo, ReOrderRepository $reorder_repo)
    {
        $this->repo = $repo;
        $this->reorder_repo = $reorder_repo;
    }

    public function store($request, $type = null)
    {
        // Default to the generic category type if nothing was supplied
        $type = $type ?? CategoryType::CATEGORY->value;

        $this->repo->checkCategoryTypeExists($type);

        // Use the decoded type for category creation
        $category = $this->repo->createCategory($request, $type);

        $this->repo->storeMetaData($category, $request);

        // Fix method name - use updateOrCreateMeta instead of createOrUpdateMeta
        $this->repo->updateOrCreateMeta($category, 'seo_title', $request->seo_title ?? null);
        $this->repo->updateOrCreateMeta($category, 'seo_description', $request->seo_description ?? null);

        return $category;
    }

    public function getCategoryByType($type)
    {
        return $this->repo->getCategoryByType($type);
    }


    public function getCategoryByTypeOrdered($type)
    {
        return $this->repo->getCategoriesOrderedByPosition($type);
    }

    public function getCategoryById($id)
    {
        return $this->repo->getCategoryById($id);
    }

    public function deleteCategory($id)
    {
        try {
            return $this->repo->deleteCategory($id);
        } catch (\Exception $e) {
            throw new \Exception('Failed to delete category: ' . $e->getMessage());
        }
    }

    public function reorderCategories(array $categoryIds)
    {
        try {
            DB::transaction(function () use ($categoryIds) {
                foreach ($categoryIds as $position => $categoryId) {
                    Category::where('id', $categoryId)->update([
                        'position' => $position + 1
                    ]);
                }
            });
            return true;
        } catch (\Exception $e) {
            throw new \Exception('Failed to reorder categories: ' . $e->getMessage());
        }
    }
}
