<?php

namespace App\Repositories;

use App\Enums\PostType;
use App\Models\Category;
use App\Models\Post;
use App\Traits\SlugGenerateTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use function Avifinfo\read;

class PostRepository
{
    use SlugGenerateTrait;

    public function checkPostTypeExists($type)
    {
        if (!in_array($type, PostType::toArray())) {
            abort(403, 'Post Type Not Found');
        }

        return true;
    }

    public function getAllPost($type)
    {
        return Post::with('categories', 'postMeta')->where('type', $type)->get();
    }

    public function getPostByStatus($type, $status)
    {
        return Post::where('post_type', $type)->where('status', $status)->get();
    }

    // get all trashed posts
    public function getTrashedPosts($type)
    {
        return Post::onlyTrashed()->where('post_type', $type)->get();
    }

    // get posts by category
    public function getPostByCategory($type)
    {
        return Category::where('type', $type);
    }


    // create new post
    public function createPost($request, $type)
    {
        return DB::transaction(function () use ($request, $type) {

            $model = new Post();

            // Create the post
            $post = Post::create([
                'user_id' => Auth::user()->id,
                'post_title' => $request->post_name,
                'slug' => $this->createSlug($request->post_name, $request->slug, $model),
                'post_content' => $request->post_content ?? null,
                'post_excerpt' => $request->post_excerpt ?? null,
                'post_status' => in_array($request->post_status, ['publish', 'draft']) ? $request->post_status : 'publish',
                'post_parent' => isset($request->post_parent) ?  $request->post_parent : 0,
                'post_type' => $type ?? 'post',
                'comment_status' => $request->comment_status ?? 'open',
                'menu_order' => $request->menu_order ?? 0,
                'post_password' => $request->post_password ?? null,
            ]);

            // Insert or update the `last_updated_by` meta
            $post->postMeta()->updateOrInsert(
                ['post_id' => $post->id, 'meta_key' => 'last_updated_by'],
                ['meta_value' => auth()->user()->id]
            );

            return $post;
        });
    }

    // insert or update meta data
    public function storeMetaData($payload, $request)
    {
        $metaDatas = [];
        $metaDatas['seo_title'] = isset($request->seo_title) ? $request->seo_title : NULL;
        $metaDatas['seo_description'] = isset($request->seo_description) ? $request->seo_description : NULL;
        $metaDatas['featured_image'] = isset($request->featured_image) ? $request->featured_image : NULL;
        // add meta data as per form data

        // insert or update meta data
        foreach ($metaDatas as $key => $value) {
            $payload->postMeta()->updateOrInsert(
                ['post_id' => $payload->id, 'meta_key' => $key],
                ['meta_value' => $value]
            );
        }
        // $this->bulkInserOrUpdate($payload, $metaDatas);
    }

    // update or create post meta
    public function updateOrCreateMeta($post, $key, $value)
    {
        $post->postMeta()->updateOrInsert(
            ['post_id' => $post->id, 'meta_key' => $key],
            ['meta_value' => $value]
        );
    }

    public function permanentDelete($id)
    {
        $post = Post::withTrashed()->findOrFail($id);

        if (!empty($post)) {
            $post->forceDelete();
        }

        return true;
    }

    // make post type base64_encode
    public function encodeType($type)
    {
        return base64_encode($type);
    }

    // make post type base64_decode
    public function decodeType($type)
    {
        return base64_decode($type);
    }
}
