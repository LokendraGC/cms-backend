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
        Schema::create('post_metas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id')->default(0);
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->string('meta_key');
            $table->longText('meta_value')->nullable();

            $table->timestamps = false;

            // Indexing
            $table->index('post_id');
            $table->index('meta_key');

            // Add unique constraint on post_id and meta_key
            $table->unique(['post_id', 'meta_key']);
        });

        // create for home
        DB::table('post_metas')->insert([
            'post_id' => 1,
            'meta_key' => 'page_template',
            'meta_value' => 'home',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_metas', function (Blueprint $table) {
            $table->dropIndex(['post_metas_post_id_index']);
            $table->dropIndex(['post_metas_meta_key_index']);
        });

        Schema::dropIfExists('post_metas');
    }
};
