@extends('layouts.app')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card" style="border-radius: 10px;">
            <div class="header">
                <h2>DETAIL PENJUALAN</h2>
                <div class="pull-right">
                    <a href="{{ route('penjualan.index') }}" class="btn btn-default" style="border-radius: 5px; margin-right: 8px;">
                        <i class="material-icons">arrow_back</i>
                    </a>
                    @if($penjualan)
                        <a href="{{ route('penjualan.cetakStruk', ['kode_pjl' => $penjualan->kode_penjualan]) }}" target="_blank" class="btn btn-success" style="border-radius: 5px;">
                            <i class="material-icons">print</i> Cetak Ulang
                        </a>
                    @endif
                </div>
            </div>
            <div class="body">
                @if(!$penjualan)
                    <div class="alert alert-warning" role="alert">
                        Data transaksi tidak ditemukan
                    </div>
                @else
                    @php
                        $waktu = null;
                        if (!empty($detail?->waktu_transaksi)) {
                            $waktu = \Carbon\Carbon::parse($detail->waktu_transaksi)->timezone('Asia/Jakarta')->format('d-m-Y H:i');
                        } else {
                            $waktu = \Carbon\Carbon::parse($penjualan->tgl_penjualan)->timezone('Asia/Jakarta')->format('d-m-Y');
                        }
                        $total = $items->sum('total');
                        $diskonGlobal = (int) ($detail->diskon ?? 0);
                        $subTotal = $total - $diskonGlobal;
                        $pajakNominal = (int) ($detail->pajak_nominal ?? 0);
                        $totalAkhir = $subTotal + $pajakNominal;
                    @endphp

                    <div class="row clearfix">
                        <div class="col-md-6">
                            <table class="table">
                                <tr>
                                    <th width="140">No. Nota</th>
                                    <td>{{ $penjualan->kode_penjualan }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal</th>
                                    <td>{{ $waktu }}</td>
                                </tr>
                                <tr>
                                    <th>Pelanggan</th>
                                    <td>{{ $penjualan->pelanggan_nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Kasir</th>
                                    <td>{{ $penjualan->kasir_nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Metode Bayar</th>
                                    <td>{{ $metodePembayaranNama }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="50">No</th>
                                    <th>Produk</th>
                                    <th width="120">Harga</th>
                                    <th width="100">Jumlah</th>
                                    <th width="140">Diskon/Item</th>
                                    <th width="140">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $index => $item)
                                    @php
                                        $hargaAwal = (int) ($item->barang->harga_jual ?? 0);
                                        $potItem = (int) ($item->potongan_item ?? 0);
                                        $hargaAkhir = max(0, $hargaAwal - $potItem);
                                        $diskonText = '-';
                                        if ($potItem > 0) {
                                            if (($item->diskon_tipe ?? 'rupiah') === 'persen') {
                                                $diskonText = (int) $item->diskon_item . '% (Rp ' . number_format($potItem, 0, ',', '.') . ')';
                                            } else {
                                                $diskonText = 'Rp ' . number_format($potItem, 0, ',', '.');
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            {{ $item->barang->nama_barang ?? '-' }}
                                            <div class="text-muted" style="font-size: 12px;">{{ $item->kode_barcode }}</div>
                                        </td>
                                        <td class="text-right">
                                            @if($potItem > 0)
                                                <div class="text-muted" style="text-decoration: line-through;">
                                                    Rp {{ number_format($hargaAwal, 0, ',', '.') }}
                                                </div>
                                                <div>
                                                    Rp {{ number_format($hargaAkhir, 0, ',', '.') }}
                                                </div>
                                            @else
                                                Rp {{ number_format($hargaAwal, 0, ',', '.') }}
                                            @endif
                                        </td>
                                        <td class="text-right">{{ $item->jumlah }}</td>
                                        <td class="text-right">{{ $diskonText }}</td>
                                        <td class="text-right">Rp {{ number_format((int) $item->total, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row clearfix" style="margin-top: 10px;">
                        <div class="col-md-4 col-md-offset-8">
                            <table class="table">
                                <tr>
                                    <th class="text-right">Total</th>
                                    <td class="text-right">Rp {{ number_format((int) $total, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th class="text-right">Diskon</th>
                                    <td class="text-right">Rp {{ number_format((int) $diskonGlobal, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th class="text-right">Sub Total</th>
                                    <td class="text-right"><strong>Rp {{ number_format((int) $subTotal, 0, ',', '.') }}</strong></td>
                                </tr>
                                @if($pajakNominal > 0)
                                    <tr>
                                        <th class="text-right">Pajak</th>
                                        <td class="text-right">Rp {{ number_format((int) $pajakNominal, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-right">Total Akhir</th>
                                        <td class="text-right"><strong>Rp {{ number_format((int) $totalAkhir, 0, ',', '.') }}</strong></td>
                                    </tr>
                                @endif
                                <tr>
                                    <th class="text-right">Bayar</th>
                                    <td class="text-right">Rp {{ number_format((int) ($detail->bayar ?? 0), 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th class="text-right">Kembali</th>
                                    <td class="text-right">Rp {{ number_format((int) ($detail->kembali ?? 0), 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
