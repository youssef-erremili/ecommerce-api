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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('product_name');
            $table->string('slug')->nullable();
            $table->text('description');
            $table->integer('price');
            $table->integer('quantity')->default(0);
            $table->integer('discount')->default(0);
            $table->foreignId('category_id ')->constrained('categories')->onDelete('cascade');
            $table->string('product_images ');
            $table->integer('category_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
