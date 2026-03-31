<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Category;
use App\Models\FnbRecipe;
use Illuminate\Http\Request;

class FnbHppReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hpp.view');
    }

    public function index(Request $request)
    {
        $categoryId = $request->get('category_id');
        $categories = Category::orderBy('nama_kategori')->get();

        $data = $this->buildReportData($categoryId);

        return view('fnb.hpp_report.index', compact('data', 'categories', 'categoryId'));
    }

    public function print(Request $request)
    {
        $categoryId = $request->get('category_id');
        $data = $this->buildReportData($categoryId);

        return view('fnb.hpp_report.print', compact('data'));
    }

    private function buildReportData(?string $categoryId): array
    {
        $query = Barang::where('is_menu', true);
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $menus = $query->with(['category', 'unit'])->get();

        $rows = [];
        $totalHpp = 0;
        $totalMargin = 0;
        $menuTanpaResep = 0;

        foreach ($menus as $menu) {
            $recipe = FnbRecipe::where('menu_id', $menu->kode_barcode)
                ->orderByDesc('updated_at')
                ->first();

            $hpp = $recipe?->hpp_per_porsi ?? null;
            $hargaJual = (int) $menu->harga_jual;

            if ($hpp !== null) {
                $marginNominal = $hargaJual - $hpp;
                $marginPersen = $hargaJual > 0 ? round(($marginNominal / $hargaJual) * 100, 2) : 0;
                $totalHpp += $hpp;
                $totalMargin += $marginPersen;
            } else {
                $marginNominal = null;
                $marginPersen = null;
                $menuTanpaResep++;
            }

            $rows[] = [
                'nama_barang'    => $menu->nama_barang,
                'kategori'       => $menu->category?->nama_kategori ?? '-',
                'harga_jual'     => $hargaJual,
                'hpp'            => $hpp,
                'margin_nominal' => $marginNominal,
                'margin_persen'  => $marginPersen,
                'resep_nama'     => $recipe?->nama_resep,
            ];
        }

        // Urutkan margin_persen DESC (null di bawah)
        usort($rows, function ($a, $b) {
            if ($a['margin_persen'] === null) return 1;
            if ($b['margin_persen'] === null) return -1;
            return $b['margin_persen'] <=> $a['margin_persen'];
        });

        $countWithRecipe = count($rows) - $menuTanpaResep;
        $avgHpp = $countWithRecipe > 0 ? round($totalHpp / $countWithRecipe) : 0;
        $avgMargin = $countWithRecipe > 0 ? round($totalMargin / $countWithRecipe, 2) : 0;

        return [
            'rows'            => $rows,
            'avg_hpp'         => $avgHpp,
            'avg_margin'      => $avgMargin,
            'menu_tanpa_resep' => $menuTanpaResep,
            'total_menu'      => count($rows),
        ];
    }
}
