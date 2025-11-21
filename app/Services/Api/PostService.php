<?php

namespace App\Services\Api;

use App\Repositories\PostRepository;
use App\Repositories\ReOrderRepository;

class PostService
{
    protected $repo;
    protected $reorder_repo;

    public function __construct(PostRepository $repo, ReOrderRepository $reorder_repo)
    {
        $this->repo = $repo;
        $this->reorder_repo = $reorder_repo;
    }

    public function decodeType($type)
    {
        return $this->repo->decodeType($type);
    }

    public function createOrUpdateMeta($post, $key, $value)
    {
        return $this->repo->updateOrCreateMeta($post, $key, $value);
    }
    public function checkPostTypeExists($type)
    {
        return $this->repo->checkPostTypeExists($type);
    }

    public function storePost($request, $default_type)
    {


        // decode request type
        $type = $request->type
            ? $this->decodeType($request->type)
            : 'NOT FOUND';

        // check valid type
        $this->checkPostTypeExists($type);

        // create post
        $post = $this->repo->createPost($request, $default_type);

        $this->createOrUpdateMeta($post, 'seo_title', $request->seo_title);
        $this->createOrUpdateMeta($post, 'seo_description', $request->seo_description);


        return $post;
    }

    public function storeMetaData($payload, $request)
    {
        return $this->repo->storeMetaData($payload, $request);
    }

    public function reorderPosts(array $postIds)
    {
        try {
            return $this->reorder_repo->reorderItems($postIds, 'App\Models\Post');
        } catch (\Exception $e) {
            throw new \Exception('Failed to reorder posts: ' . $e->getMessage());
        }
    }
}
