<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
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

    public function index(Request $request)
    {
        $type = $request->query('type', 'category');

        try {
            $data = $this->category_service->getCategoryByTypeOrdered($type);

            return $this->response_service->successMessage(
                data: $data,
                message: 'Categories fetched successfully',
                code: 200,
            );
        } catch (\Exception $err) {
            return $this->response_service->errorMessage(
                message: 'Error fetching categories: ' . $e->getMessage(),
                code: 500
            );
        }
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
    public function store(CategoryRequest $request)
    {
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

    public function reorder(Request $request)
    {
        \Log::info('Reorder request received', $request->all());

        try {
            // Simple validation
            if (!$request->has('order') || !is_array($request->order)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Order array is required'
                ], 400);
            }

            // Simple update - no validation for now
            foreach ($request->order as $position => $categoryId) {
                \DB::table('categories')->where('id', $categoryId)->update([
                    'position' => $position + 1,
                    'updated_at' => now()
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Categories reordered successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Reorder error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to reorder categories: ' . $e->getMessage()
            ], 500);
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
        try {
            $this->category_service->deleteCategory($id);

            return $this->response_service->successMessage(
                message: 'Category deleted successfully',
                code: 200
            );
        } catch (\Exception $e) {
            return $this->response_service->errorMessage(
                message: 'Failed to delete category: ' . $e->getMessage(),
                code: 500
            );
        }
    }
}
