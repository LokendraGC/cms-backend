<?php

namespace App\Services\Api;

use App\Enums\CategoryType;
use App\Repositories\CategoryMetaRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\ReOrderRepository;
use Exception;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    protected $repo;
    protected $reorder_repo;
    protected $metaRepo;

    public function __construct(CategoryRepository $repo, ReOrderRepository $reorder_repo, CategoryMetaRepository $metaRepo)
    {
        $this->repo = $repo;
        $this->reorder_repo = $reorder_repo;
        $this->metaRepo = $metaRepo;
    }

    public function store($request, $type)
    {
        try {
            // Create category
            $category = $this->repo->createCategoryWithMeta($request, $type);

            // Process and save meta data - ONLY if metaRepo exists
            if (isset($this->metaRepo)) {
                $metaDatas = $this->metaRepo->processMetaData($category, $request);

                foreach ($metaDatas as $key => $value) {
                    $this->repo->updateOrCreateMeta($category, $key, $value);
                }
            }

            return true;
        } catch (Exception $e) {
            throw new Exception('Failed to create category: ' . $e->getMessage());
        }
    }


    public function getCategoryByType($type)
    {
        return $this->repo->getCategoryByType($type);
    }

    public function getCategoriesWithMeta($type)
    {
        return $this->repo->getCategoriesWithMeta($type);
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
