<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('category_post', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->default(0);
            $table->unsignedBigInteger('post_id')->default(0);


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('category_post', function (Blueprint $table) {
            $table->dropIndex(['category_post_post_id_index']);
            $table->dropIndex(['category_post_category_id_index']);
            $table->dropUnique(['category_post_post_id_category_id_unique']);
        });

        Schema::dropIfExists('category_post');
    }
};
