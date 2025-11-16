<?php

namespace App\Services\Api;

use App\Repositories\CategoryRepository;

class CategoryService
{
    protected $repo;

    public function __construct(CategoryRepository $repo)
    {
        $this->repo = $repo;
    }

    public function store($request, $encoded_type)
    {

        // Decode the type first
        $type = $this->repo->decodeType($encoded_type);

        $this->repo->checkCategoryTypeExists($type);

        // Use the decoded type for category creation
        $category = $this->repo->createCategory($request, $type);

        $this->repo->storeMetaData($category, $request);

        // Fix method name - use updateOrCreateMeta instead of createOrUpdateMeta
        $this->repo->updateOrCreateMeta($category, 'seo_title', $request->seo_title ?? null);
        $this->repo->updateOrCreateMeta($category, 'seo_description', $request->seo_description ?? null);

        return $category;
    }
}
