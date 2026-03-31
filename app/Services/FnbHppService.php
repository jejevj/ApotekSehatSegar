<?php

namespace App\Services;

use App\Models\FnbIngredient;
use App\Models\FnbIngredientStockLog;
use App\Models\FnbRecipe;
use App\Models\FnbRecipeItem;
use App\Models\Penjualan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FnbHppService
{
    /**
     * Hitung HPP satu resep.
     * Formula: floor(SUM(harga_beli * qty) / porsi)
     */
    public function calculateHpp(FnbRecipe $recipe): int
    {
        $items = $recipe->items()->with('ingredient')->get();

        if ($items->isEmpty()) {
            return 0;
        }

        $total = 0;
        foreach ($items as $item) {
            if ($item->ingredient) {
                $total += $item->ingredient->harga_beli * (float) $item->qty;
            }
        }

        $porsi = max(1, (int) $recipe->porsi);
        return (int) floor($total / $porsi);
    }

    /**
     * Recalculate HPP untuk semua resep yang menggunakan ingredient ini.
     */
    public function recalculateForIngredient(int $ingredientId): void
    {
        $recipeIds = FnbRecipeItem::where('ingredient_id', $ingredientId)
            ->pluck('recipe_id')
            ->unique();

        foreach ($recipeIds as $recipeId) {
            try {
                $recipe = FnbRecipe::withoutGlobalScopes()->with('items.ingredient')->find($recipeId);
                if (!$recipe) continue;

                $hpp = $this->calculateHpp($recipe);
                $recipe->update(['hpp_per_porsi' => $hpp, 'hpp_outdated' => false]);
            } catch (\Throwable $e) {
                Log::error("FnbHppService: gagal recalculate HPP untuk recipe #{$recipeId}: " . $e->getMessage());
                FnbRecipe::withoutGlobalScopes()->where('id', $recipeId)->update(['hpp_outdated' => true]);
            }
        }
    }

    /**
     * Kurangi stok bahan baku saat transaksi penjualan selesai.
     * Non-blocking: jika stok tidak cukup, log warning tapi tetap lanjut.
     */
    public function deductStockForTransaction(string $kodePenjualan): void
    {
        $storeId = \App\Services\StoreContext::getStoreId();

        // Ambil semua item penjualan
        $items = Penjualan::where('kode_penjualan', $kodePenjualan)->get();

        if ($items->isEmpty()) return;

        DB::transaction(function () use ($items, $kodePenjualan, $storeId) {
            foreach ($items as $item) {
                // Cari resep aktif untuk menu ini di toko ini
                $recipe = FnbRecipe::withoutGlobalScopes()
                    ->where('store_id', $storeId)
                    ->where('menu_id', $item->kode_barcode)
                    ->with('items.ingredient')
                    ->first();

                if (!$recipe) continue;

                $jumlahTerjual = (int) $item->jumlah;

                foreach ($recipe->items as $recipeItem) {
                    $ingredient = $recipeItem->ingredient;
                    if (!$ingredient) continue;

                    $qtyDeduct = (float) $recipeItem->qty * $jumlahTerjual;

                    if ($ingredient->stok < $qtyDeduct) {
                        Log::warning("FnbHppService: stok bahan baku '{$ingredient->nama}' tidak cukup. " .
                            "Dibutuhkan: {$qtyDeduct}, tersedia: {$ingredient->stok}. " .
                            "Transaksi: {$kodePenjualan}");
                    }

                    // Kurangi stok (bisa minus jika tidak cukup — non-blocking)
                    FnbIngredient::withoutGlobalScopes()
                        ->where('id', $ingredient->id)
                        ->decrement('stok', $qtyDeduct);

                    // Catat log
                    FnbIngredientStockLog::create([
                        'ingredient_id'  => $ingredient->id,
                        'store_id'       => $storeId,
                        'qty_change'     => -$qtyDeduct,
                        'type'           => 'sale',
                        'reference_id'   => $kodePenjualan,
                        'reference_type' => 'penjualan',
                        'keterangan'     => "Penjualan {$kodePenjualan}",
                    ]);
                }
            }
        });
    }

    /**
     * Kembalikan stok bahan baku saat transaksi dibatalkan.
     */
    public function restoreStockForTransaction(string $kodePenjualan): void
    {
        $storeId = \App\Services\StoreContext::getStoreId();

        // Ambil log penjualan terkait
        $logs = FnbIngredientStockLog::withoutGlobalScopes()
            ->where('store_id', $storeId)
            ->where('reference_id', $kodePenjualan)
            ->where('reference_type', 'penjualan')
            ->where('type', 'sale')
            ->get();

        if ($logs->isEmpty()) return;

        DB::transaction(function () use ($logs, $kodePenjualan, $storeId) {
            foreach ($logs as $log) {
                // Kembalikan stok (qty_change negatif, jadi increment dengan abs)
                FnbIngredient::withoutGlobalScopes()
                    ->where('id', $log->ingredient_id)
                    ->increment('stok', abs((float) $log->qty_change));

                // Catat log adjustment
                FnbIngredientStockLog::create([
                    'ingredient_id'  => $log->ingredient_id,
                    'store_id'       => $storeId,
                    'qty_change'     => abs((float) $log->qty_change),
                    'type'           => 'adjustment',
                    'reference_id'   => $kodePenjualan,
                    'reference_type' => 'penjualan_cancel',
                    'keterangan'     => "Pembatalan transaksi {$kodePenjualan}",
                ]);
            }
        });
    }
}
