<?php

namespace App\Http\Controllers;

use App\Models\BillingSetting;
use Illuminate\Http\Request;

class BillingSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:billing.manage');
    }

    public function index()
    {
        return view('billing.index');
    }

    public function data()
    {
        $items = \App\Models\BillingSetting::query();
        $expiredActive = $this->isExpiredActive();
        return \Yajra\DataTables\Facades\DataTables::of($items)
            ->addIndexColumn()
            ->addColumn('expired_at_fmt', function ($row) {
                return $row->expired_at ? $row->expired_at->timezone('Asia/Jakarta')->format('d-m-Y H:i') : '-';
            })
            ->addColumn('status', function ($row) {
                if ($row->status === 'aktif') return '<span class="label bg-green">Aktif</span>';
                if ($row->status === 'sudah_dibayar') return '<span class="label bg-blue">Sudah Dibayar</span>';
                if ($row->status === 'kedaluwarsa') return '<span class="label bg-red">Kedaluwarsa</span>';
                return '<span class="label bg-grey">-</span>';
            })
            ->addColumn('aksi', function ($row) {
                $expiredActive = $this->isExpiredActive();
                $btn = '';
                // Saat jatuh tempo: izinkan Edit, Hapus (non-aktif/kedaluwarsa), dan tandai "Sudah Dibayar" pada billing aktif
                if ($expiredActive) {
                    // Edit selalu diizinkan
                    $btn .= '<a href="'.route('billing.edit', $row->id).'" class="btn btn-success btn-xs"><i class="material-icons">edit</i></a> ';
                    // Hapus diizinkan jika bukan aktif
                    if ($row->status !== 'aktif') {
                        $btn .= '<form action="'.route('billing.destroy', $row->id).'" method="POST" style="display:inline;" onsubmit="return confirm(\'Hapus billing ini?\')">'
                             . csrf_field()
                             . method_field('DELETE')
                             . '<button type="submit" class="btn btn-danger btn-xs"><i class="material-icons">delete</i></button>'
                             . '</form> ';
                    }
                    if ($row->status === 'aktif') {
                        $btn .= ' <form action="'.route('billing.status', [$row->id, 'sudah_dibayar']).'" method="POST" style="display:inline;">'
                             . csrf_field()
                             . method_field('PATCH')
                             . '<button type="submit" class="btn btn-info btn-xs" onclick="return confirm(\'Tandai Sudah Dibayar?\')"><i class="material-icons">done_all</i></button>'
                             . '</form>';
                    }
                    return $btn ?: '-';
                }

                // Normal
                $btn .= '<a href="'.route('billing.edit', $row->id).'" class="btn btn-success btn-xs"><i class="material-icons">edit</i></a> ';
                // Hapus diizinkan jika bukan aktif
                if ($row->status !== 'aktif') {
                    $btn .= '<form action="'.route('billing.destroy', $row->id).'" method="POST" style="display:inline;" onsubmit="return confirm(\'Hapus billing ini?\')">'
                         . csrf_field()
                         . method_field('DELETE')
                         . '<button type="submit" class="btn btn-danger btn-xs"><i class="material-icons">delete</i></button>'
                         . '</form> ';
                }
                if ($row->status !== 'aktif') {
                    $btn .= '<form action="'.route('billing.activate', $row->id).'" method="POST" style="display:inline;">'
                         . csrf_field()
                         . method_field('PATCH')
                         . '<button type="submit" class="btn btn-primary btn-xs" onclick="return confirm(\'Jadikan Aktif?\')"><i class="material-icons">check_circle</i></button>'
                         . '</form>';
                }
                if ($row->status !== 'sudah_dibayar') {
                    $btn .= ' <form action="'.route('billing.status', [$row->id, 'sudah_dibayar']).'" method="POST" style="display:inline;">'
                         . csrf_field()
                         . method_field('PATCH')
                         . '<button type="submit" class="btn btn-info btn-xs" onclick="return confirm(\'Tandai Sudah Dibayar?\')"><i class="material-icons">done_all</i></button>'
                         . '</form>';
                }
                if ($row->status !== 'kedaluwarsa') {
                    $btn .= ' <form action="'.route('billing.status', [$row->id, 'kedaluwarsa']).'" method="POST" style="display:inline;">'
                         . csrf_field()
                         . method_field('PATCH')
                         . '<button type="submit" class="btn btn-warning btn-xs" onclick="return confirm(\'Tandai Kedaluwarsa?\')"><i class="material-icons">schedule</i></button>'
                         . '</form>';
                }
                return $btn;
            })
            ->rawColumns(['status','aksi'])
            ->make(true);
    }

    public function create()
    {
        return view('billing.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'expired_at' => 'required|date',
            'jumlah_tagihan' => 'required|integer|min:0',
            'nama_bank' => 'required|string|max:255',
            'no_rek' => 'required|string|max:255',
            'status' => 'required|in:aktif,sudah_dibayar,kedaluwarsa',
        ]);

        $setting = new \App\Models\BillingSetting($request->only(['expired_at','jumlah_tagihan','nama_bank','no_rek']));
        $setting->status = $request->status;
        $setting->is_active = $request->status === 'aktif';
        $setting->save();

        if ($setting->status === 'aktif') {
            \App\Models\BillingSetting::where('id','<>',$setting->id)->update(['is_active' => false, 'status' => 'kedaluwarsa']);
        }

        return redirect()->route('billing.index')->with('success', 'Billing berhasil dibuat');
    }

    public function edit()
    {
        $billing = BillingSetting::where('status', 'aktif')->orderBy('id', 'desc')->first();
        if (!$billing) {
            $billing = BillingSetting::query()->orderBy('id', 'desc')->first();
        }
        if (!$billing) {
            $billing = BillingSetting::create([
                'expired_at' => now()->addDays(7),
                'jumlah_tagihan' => 0,
                'nama_bank' => null,
                'no_rek' => null,
                'is_active' => false,
                'status' => 'kedaluwarsa',
            ]);
        }
        return view('billing.setting', ['billing' => $billing]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'expired_at' => 'required|date',
            'jumlah_tagihan' => 'required|integer|min:0',
            'nama_bank' => 'required|string|max:255',
            'no_rek' => 'required|string|max:255',
            'status' => 'required|in:aktif,sudah_dibayar,kedaluwarsa',
        ]);

        $setting = BillingSetting::where('status', 'aktif')->orderBy('id', 'desc')->first();
        if (!$setting) {
            $setting = BillingSetting::query()->orderBy('id', 'desc')->first();
        }
        if (!$setting) {
            $setting = new BillingSetting();
        }
        $setting->expired_at = $request->expired_at;
        $setting->jumlah_tagihan = $request->jumlah_tagihan;
        $setting->nama_bank = $request->nama_bank;
        $setting->no_rek = $request->no_rek;
        $setting->status = $request->status;
        $setting->is_active = $request->status === 'aktif';
        $setting->save();

        if ($setting->status === 'aktif') {
            BillingSetting::where('id','<>',$setting->id)->update(['is_active' => false, 'status' => 'kedaluwarsa']);
        }

        return redirect()->route('billing.index')->with('success', 'Billing settings updated');
    }

    public function editItem($id)
    {
        $billing = BillingSetting::findOrFail($id);
        return view('billing.edit', ['billing' => $billing]);
    }

    public function updateItem(Request $request, $id)
    {
        $setting = BillingSetting::findOrFail($id);
        $request->validate([
            'expired_at' => 'required|date',
            'jumlah_tagihan' => 'required|integer|min:0',
            'nama_bank' => 'required|string|max:255',
            'no_rek' => 'required|string|max:255',
            'status' => 'required|in:aktif,sudah_dibayar,kedaluwarsa',
        ]);
        $setting->update([
            'expired_at' => $request->expired_at,
            'jumlah_tagihan' => $request->jumlah_tagihan,
            'nama_bank' => $request->nama_bank,
            'no_rek' => $request->no_rek,
            'status' => $request->status,
            'is_active' => $request->status === 'aktif',
        ]);
        if ($setting->status === 'aktif') {
            BillingSetting::where('id','<>',$setting->id)->update(['is_active' => false, 'status' => 'kedaluwarsa']);
        }
        return redirect()->route('billing.index')->with('success', 'Billing updated');
    }

    public function activate($id)
    {
        $setting = BillingSetting::findOrFail($id);
        $setting->update(['is_active' => true, 'status' => 'aktif']);
        BillingSetting::where('id','<>',$setting->id)->update(['is_active' => false, 'status' => 'kedaluwarsa']);
        return redirect()->route('billing.index')->with('success', 'Billing diaktifkan');
    }

    public function setStatus($id, $status)
    {
        $setting = BillingSetting::findOrFail($id);
        if (!in_array($status, ['aktif', 'sudah_dibayar', 'kedaluwarsa'])) {
            return redirect()->route('billing.index')->with('error', 'Status tidak valid');
        }
        // Saat jatuh tempo: hanya izinkan penandaan "Sudah Dibayar"
        if ($this->isExpiredActive() && $status !== 'sudah_dibayar') {
            return redirect()->route('billing.index')->with('error', 'Tagihan jatuh tempo. Hanya dapat menandai Sudah Dibayar.');
        }
        $payload = ['status' => $status];
        if ($status === 'aktif') {
            $payload['is_active'] = true;
            $setting->update($payload);
            BillingSetting::where('id','<>',$setting->id)->update(['is_active' => false, 'status' => 'kedaluwarsa']);
        } else {
            $payload['is_active'] = false;
            $setting->update($payload);
            // Jika disetujui "sudah_dibayar" → buat billing baru untuk periode 1 tahun mendatang
            if ($status === 'sudah_dibayar') {
                $expiredBase = $setting->expired_at?->copy()->timezone('Asia/Jakarta') ?? now('Asia/Jakarta');
                $newExpired = $expiredBase->copy()->addYear()->endOfDay();
                // Nonaktifkan yang lain untuk berjaga-jaga
                BillingSetting::where('id','<>',$setting->id)->update(['is_active' => false]);
                BillingSetting::create([
                    'expired_at' => $newExpired,
                    'jumlah_tagihan' => $setting->jumlah_tagihan,
                    'nama_bank' => $setting->nama_bank,
                    'no_rek' => $setting->no_rek,
                    'is_active' => true,
                    'status' => 'aktif',
                ]);
            }
        }
        return redirect()->route('billing.index')->with('success', 'Status diperbarui');
    }

    public function destroy($id)
    {
        $billing = BillingSetting::findOrFail($id);
        // Hindari menghapus billing aktif terakhir
        if ($billing->status === 'aktif') {
            return redirect()->route('billing.index')->with('error', 'Tidak dapat menghapus billing yang berstatus aktif');
        }
        $billing->delete();
        return redirect()->route('billing.index')->with('success', 'Billing berhasil dihapus');
    }

    private function isExpiredActive(): bool
    {
        $active = BillingSetting::where('status', 'aktif')->orderBy('id', 'desc')->first();
        if (!$active || !$active->expired_at) {
            return false;
        }
        $expiredAt = $active->expired_at->timezone('Asia/Jakarta')->copy()->startOfDay();
        $today = now('Asia/Jakarta')->startOfDay();
        return $today->greaterThanOrEqualTo($expiredAt);
    }
}
