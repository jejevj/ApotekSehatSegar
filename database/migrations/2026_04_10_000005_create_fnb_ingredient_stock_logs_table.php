<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fnb_ingredient_stock_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ingredient_id');
            $table->unsignedBigInteger('store_id');
            $table->decimal('qty_change', 10, 3);
            $table->enum('type', ['sale', 'purchase', 'adjustment']);
            $table->string('reference_id')->nullable();
            $table->string('reference_type')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('ingredient_id')->references('id')->on('fnb_ingredients')->onDelete('cascade');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fnb_ingredient_stock_logs');
    }
};
