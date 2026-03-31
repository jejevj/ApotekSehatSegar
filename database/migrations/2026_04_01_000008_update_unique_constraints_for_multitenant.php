<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // roles: hapus unique lama pada slug, buat composite unique (store_id, slug)
        Schema::table('roles', function (Blueprint $table) {
            try {
                $table->dropUnique(['slug']);
            } catch (\Exception $e) {
                // Index mungkin sudah tidak ada, lanjutkan
            }

            try {
                $table->dropUnique(['name']);
            } catch (\Exception $e) {
                // Index mungkin sudah tidak ada, lanjutkan
            }

            $table->unique(['store_id', 'slug'], 'roles_store_id_slug_unique');
        });

        // business_configs: hapus unique lama pada key, buat composite unique (store_id, key)
        Schema::table('business_configs', function (Blueprint $table) {
            try {
                $table->dropUnique(['key']);
            } catch (\Exception $e) {
                // Index mungkin sudah tidak ada, lanjutkan
            }

            $table->unique(['store_id', 'key'], 'business_configs_store_id_key_unique');
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            try {
                $table->dropUnique('roles_store_id_slug_unique');
            } catch (\Exception $e) {
                //
            }
            $table->string('slug')->unique()->change();
        });

        Schema::table('business_configs', function (Blueprint $table) {
            try {
                $table->dropUnique('business_configs_store_id_key_unique');
            } catch (\Exception $e) {
                //
            }
            $table->string('key', 100)->unique()->change();
        });
    }
};
