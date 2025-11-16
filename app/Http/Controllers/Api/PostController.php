<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\CategoryPostRepository;
use App\Services\Api\CategoryService;
use App\Services\Api\PostService;
use App\Services\Api\ResponseService;
use Illuminate\Http\Request;

class PostController extends Controller
{

    protected $post_service;
    protected $postType = "post";
    protected $response_service;
    protected $category_service;
    protected $category_post_service;


    public function __construct(PostService $post_service, ResponseService $response_service, CategoryService $category_service, CategoryPostRepository $category_post_service)
    {
        $this->post_service = $post_service;
        $this->response_service = $response_service;
        $this->category_service = $category_service;
        $this->category_post_service = $category_post_service;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'post_name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'excerpt' => 'nullable|string',
            'status' => 'nullable|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'categories' => 'nullable|array',
            'categories.*' => 'integer',
        ]);

        try {

            $type = isset($request->type)
                ? $this->post_service->decodeType($request->type)
                : 'NOT FOUND';

            $this->post_service->checkPostTypeExists($type);

            $request->merge(['post_status' => $request->input('action')]);

            // store categories
            $categories = isset($request->categories) ? $request->categories : [];
            $authors = isset($request->authors) ? $request->authors : [];
            $tags = isset($request->tags) ? $request->tags : [];

            $post =  $this->post_service->storePost($request, $this->postType);

            $cats = array_unique(array_merge($categories, $authors, $tags));
            $this->category_post_service->assignCategory($post, $cats);

            $this->post_service->storeMetaData($post, $request);

            return $this->response_service->successMessage(
                message: 'Post created successfully',
                code: 201
            );
        } catch (\Exception $e) {
            return $this->response_service->errorMessage(
                message: 'Failed to create post: ' . $e->getMessage(),
                code: 500
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
