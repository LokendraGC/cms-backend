<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Api\CategoryService;
use App\Services\Api\ResponseService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    protected $category_service;
    protected $categoryType = "Category";
    protected $response_service;

    public function __construct(CategoryService $category_service, ResponseService $response_service)
    {
        $this->category_service = $category_service;
        $this->response_service = $response_service;
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
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'parent' => 'nullable|integer',
            'menu_order' => 'nullable|integer',
        ]);

        try {


            $this->category_service->store($request, $request->type);

            return $this->response_service->successMessage(
                message: 'Category created successfully',
                code: 201
            );
        } catch (\Exception $e) {
            return $this->response_service->errorMessage(
                message: 'Category creation failed: ' . $e->getMessage(),
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
