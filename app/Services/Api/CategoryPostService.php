<?php

namespace App\Services\Api;

use App\Repositories\CategoryPostRepository;

class CategoryService
{
    protected $repo;

    public function __construct(CategoryPostRepository $repo)
    {
        $this->repo = $repo;
    }

    public function assignCategory($payload, $ids)
    {
        return $this->repo->assignCategory($payload, $ids);
    }
}
