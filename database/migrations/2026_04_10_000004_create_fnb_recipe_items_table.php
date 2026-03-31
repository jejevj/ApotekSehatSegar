<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fnb_recipe_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('recipe_id');
            $table->unsignedBigInteger('ingredient_id');
            $table->decimal('qty', 10, 3);
            $table->unsignedBigInteger('unit_id');
            $table->string('keterangan')->nullable();

            $table->foreign('recipe_id')->references('id')->on('fnb_recipes')->onDelete('cascade');
            $table->foreign('ingredient_id')->references('id')->on('fnb_ingredients')->onDelete('restrict');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fnb_recipe_items');
    }
};
