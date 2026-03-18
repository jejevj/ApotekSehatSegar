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
        // Gunakan DB::statement karena Laravel Schema tidak mendukung modifikasi ENUM dengan baik
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE stock_opnames MODIFY COLUMN status ENUM('draft', 'menunggu_approve', 'selesai', 'dibatalkan') DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE stock_opnames MODIFY COLUMN status ENUM('draft', 'selesai', 'dibatalkan') DEFAULT 'draft'");
    }
};
