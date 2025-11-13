<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->text('post_title');
            $table->string('slug')->unique();
            $table->longText('post_content')->nullable();
            $table->text('post_excerpt')->nullable();
            $table->string('post_status')->default('publish');
            $table->unsignedBigInteger('post_parent')->default(0);
            $table->string('post_type')->default('post');
            $table->string('comment_status')->default('open');
            $table->integer('menu_order')->nullable();
            $table->string('post_password')->nullable();
            $table->timestamps();
            $table->softDeletes(); // For the deleted_at column

            // Indexing
            $table->index('slug');
            $table->index('post_status');
            $table->index('post_parent');
            $table->index('post_type');

            $table->index(['post_type', 'post_status', 'user_id']);
        });

        DB::table('posts')->insert([
            'user_id' => 1,
            'post_title' => 'Home',
            'slug' => 'home',
            'post_content' => null,
            'post_excerpt' => null,
            'post_status' => 'publish',
            'post_type' => 'page',
            'menu_order' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex(['posts_slug_index']);
            $table->dropIndex(['posts_post_status_index']);
            $table->dropIndex(['posts_post_parent_index']);
            $table->dropIndex(['posts_post_type_index']);
            $table->dropIndex(['posts_post_type_post_status_user_id_index']);
        });

        Schema::dropIfExists('posts');
    }
};
