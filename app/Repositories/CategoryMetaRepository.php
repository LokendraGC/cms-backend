<?php

namespace App\Repositories;

use App\Models\CategoryMeta;

class CategoryMetaRepository
{
    public function processMetaData($category, $request)
    {
        $metaDatas = [];

        // Handle file uploads
        $metaDatas['feature_image'] = $this->handleImageUpload($request->file('feature_image'), 'category_images');

        // Handle text meta fields
        $metaDatas['meta_title'] = $request->meta_title ?? null;
        $metaDatas['meta_description'] = $request->meta_description ?? null;
        $metaDatas['alternate_title'] = $request->alternate_title ?? null;

        return $metaDatas;
    }

    protected function handleImageUpload($file, $folder = 'category_images')
    {
        if (!$file) {
            return null;
        }

        $imageName = time() . '_' . $file->getClientOriginalName();
        return $file->storeAs($folder, $imageName, 'public');
    }
}
