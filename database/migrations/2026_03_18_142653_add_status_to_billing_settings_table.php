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
        Schema::table('billing_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('billing_settings', 'status')) {
                $table->enum('status', ['aktif', 'sudah_dibayar', 'kedaluwarsa'])->default('aktif')->after('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billing_settings', function (Blueprint $table) {
            if (Schema::hasColumn('billing_settings', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
