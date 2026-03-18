<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stock_opnames', function (Blueprint $table) {
            // Ubah tanggal ke datetime
            if (Schema::hasColumn('stock_opnames', 'tanggal')) {
                // MySQL enum exists; alter later for enum values
                DB::statement('ALTER TABLE stock_opnames MODIFY COLUMN tanggal DATETIME');
            }
            // Tambah kolom approval & total
            if (!Schema::hasColumn('stock_opnames', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('stock_opnames', 'approved_at')) {
                $table->dateTime('approved_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('stock_opnames', 'total_nilai_rugi')) {
                $table->bigInteger('total_nilai_rugi')->default(0)->after('finished_at');
            }
            if (!Schema::hasColumn('stock_opnames', 'total_nilai_lebih')) {
                $table->bigInteger('total_nilai_lebih')->default(0)->after('total_nilai_rugi');
            }
        });

        // Update enum status to include 'menunggu_approve'
        try {
            DB::statement("ALTER TABLE stock_opnames MODIFY COLUMN status ENUM('draft','menunggu_approve','selesai','dibatalkan') DEFAULT 'draft'");
        } catch (\Throwable $e) {
            // ignore if fails (e.g., sqlite)
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_opnames', function (Blueprint $table) {
                // Can't reliably revert DATETIME to DATE without data loss, skip
                if (Schema::hasColumn('stock_opnames', 'approved_by')) {
                    $table->dropConstrainedForeignId('approved_by');
                }
                if (Schema::hasColumn('stock_opnames', 'approved_at')) {
                    $table->dropColumn('approved_at');
                }
                if (Schema::hasColumn('stock_opnames', 'total_nilai_rugi')) {
                    $table->dropColumn('total_nilai_rugi');
                }
                if (Schema::hasColumn('stock_opnames', 'total_nilai_lebih')) {
                    $table->dropColumn('total_nilai_lebih');
                }
        });
    }
};
