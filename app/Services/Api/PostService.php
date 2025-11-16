<?php

namespace App\Services\Api;

use App\Repositories\PostRepository;

class PostService
{
    protected $repo;

    public function __construct(PostRepository $repo)
    {
        $this->repo = $repo;
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
}
