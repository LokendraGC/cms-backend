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
        Schema::create('category_metas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->default(0);
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->string('meta_key');
            $table->longText('meta_value')->nullable();

            $table->timestamps = false;

            $table->index('category_id');
            $table->index('meta_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('category_metas', function (Blueprint $table) {
            $table->dropIndex(['category_metas_category_id_index']);
            $table->dropIndex(['category_metas_meta_key_index']);
        });

        Schema::dropIfExists('category_metas');
    }
};
