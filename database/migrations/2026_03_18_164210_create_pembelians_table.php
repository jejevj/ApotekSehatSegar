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
        Schema::create('pembelians', function (Blueprint $table) {
            $table->id();
            $table->string('no_faktur')->unique();
            $table->date('tanggal');
            $table->foreignId('distributor_id')->constrained('distributors')->onDelete('cascade');
            $table->bigInteger('total')->default(0);
            $table->bigInteger('dibayar')->default(0);
            $table->bigInteger('sisa')->default(0);
            $table->enum('status', ['lunas', 'belum_lunas'])->default('belum_lunas');
            $table->string('bukti_nota')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelians');
    }
};
