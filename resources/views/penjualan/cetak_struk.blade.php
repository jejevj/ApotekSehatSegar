<!DOCTYPE html>
<html>
<head>
    <title>Cetak Struk - {{ $penjualan->kode_penjualan }}</title>
    <style>
        @page { size: 58mm auto; margin: 0; }
        * { box-sizing: border-box; }
        body { font-family: 'Courier New', Courier, monospace; width: 58mm; margin: 0; padding: 3mm 2mm; font-size: 10px; line-height: 1.2; color: #000; }
        table { width: 100%; border-collapse: collapse; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .muted { opacity: 0.9; }
        hr { border: none; border-top: 1px dashed #000; margin: 6px 0; }
        .header { margin-bottom: 6px; }
        .footer { margin-top: 8px; }
        .small { font-size: 9px; }
        .item-name { word-break: break-word; }
        .mono { font-variant-numeric: tabular-nums; }
    </style>
</head>
<body class="mono">
    <div class="header text-center">
        <div><strong>APOTEK SEHAT SEGAR</strong></div>
        <div class="small muted">Jl. Lintas Sumatera KM.0, Muara Bungo</div>
        <div class="small muted">TERIMA KASIH</div>
    </div>

    <hr>

    <table>
        <tr>
            <td class="text-left">Nota</td>
            <td class="text-right">{{ $penjualan->kode_penjualan }}</td>
        </tr>
        <tr>
            <td class="text-left">Tgl</td>
            <td class="text-right">
                {{ \Carbon\Carbon::parse($detail->waktu_transaksi ?? $penjualan->tgl_penjualan)->timezone('Asia/Jakarta')->format('d-m-Y H:i') }}
            </td>
        </tr>
        <tr>
            <td class="text-left">Plg</td>
            <td class="text-right">{{ $pelanggan->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="text-left">Ksr</td>
            <td class="text-right">{{ $user->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="text-left">Bayar</td>
            <td class="text-right">{{ $metodePembayaranNama }}</td>
        </tr>
    </table>

    <hr>

    <table>
        @php $total_bayar = 0; $diskon = 0; $bayar = 0; $kembali = 0; $pajakNominal = 0; $totalAkhir = 0; @endphp
        @foreach($items as $item)
            @php
                $hargaNormal = (int) ($item->harga_jual ?? 0);
                $hargaJual = (int) ($item->harga_jual_kustom ?? $hargaNormal);
                $potItem = (int) ($item->potongan_item ?? 0);
                $hargaAkhir = max(0, $hargaJual - $potItem);
            @endphp
            <tr>
                <td colspan="2" class="item-name">{{ $item->nama_barang }}</td>
            </tr>
            <tr>
                <td class="text-left">
                    @if($item->harga_jual_kustom || $potItem > 0)
                        <span class="small muted"><del>Rp {{ number_format($hargaNormal, 0, ',', '.') }}</del></span>
                        <span>Rp {{ number_format($hargaAkhir, 0, ',', '.') }}</span>
                        <span>x {{ $item->jumlah }}</span>
                    @else
                        Rp {{ number_format($hargaJual, 0, ',', '.') }} x {{ $item->jumlah }}
                    @endif
                </td>
                <td class="text-right">{{ number_format($item->line_total, 0, ',', '.') }}</td>
            </tr>
            @php 
                $total_bayar += $item->line_total;
                $diskon = $item->diskon_global;
                $bayar = $item->bayar;
                $kembali = $item->kembali;
                $pajakNominal = (int) ($item->pajak_nominal ?? 0);
                $totalAkhir = (int) ($item->total_akhir ?? 0);
            @endphp
        @endforeach
    </table>

    <hr>

    <table>
        <tr>
            <td class="text-left">Total</td>
            <td class="text-right">{{ number_format($total_bayar, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="text-left">Diskon</td>
            <td class="text-right">Rp {{ number_format((int) $diskon, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="text-left"><strong>Sub Total</strong></td>
            <td class="text-right"><strong>{{ number_format($total_bayar - (int) $diskon, 0, ',', '.') }}</strong></td>
        </tr>
        @if($pajakNominal > 0)
            <tr>
                <td class="text-left">Pajak</td>
                <td class="text-right">Rp {{ number_format($pajakNominal, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="text-left"><strong>Total Akhir</strong></td>
                <td class="text-right"><strong>{{ number_format($totalAkhir, 0, ',', '.') }}</strong></td>
            </tr>
        @endif
        <tr>
            <td class="text-left">Bayar</td>
            <td class="text-right">{{ number_format($bayar, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="text-left">Kembali</td>
            <td class="text-right">{{ number_format($kembali, 0, ',', '.') }}</td>
        </tr>
    </table>

    <hr>

    <div class="footer text-center">
        <div class="small muted">Barang yang sudah dibeli tidak dapat dikembalikan</div>
    </div>
    <script>
        window.addEventListener('load', function () {
            window.print();
        });
        window.addEventListener('afterprint', function () {
            if (window.opener) {
                try { window.close(); } catch (e) {}
                return;
            }
            window.location.href = '{{ route('penjualan.index') }}';
        });
    </script>
</body>
</html>
