<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan ({{ $tgl_awal }} s/d {{ $tgl_akhir }})</title>
    <style>
        body { font-family: 'Arial', sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; background-color: #f4f7f6; }
        .container { max-width: 1000px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 2px solid #eee; margin-bottom: 30px; padding-bottom: 20px; }
        .header h1 { margin: 0; color: #2c3e50; font-size: 24px; text-transform: uppercase; }
        .header p { margin: 5px 0; color: #7f8c8d; }
        .info { margin-bottom: 20px; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 12px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f8f9fa; color: #2c3e50; font-weight: bold; }
        tr:nth-child(even) { background-color: #fafafa; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-section { float: right; width: 300px; margin-top: 20px; }
        .total-section table { border: none; }
        .total-section table td { border: none; padding: 5px; }
        .total-section .grand-total { font-size: 16px; font-weight: bold; color: #e91e63; }
        .no-print { margin-bottom: 20px; display: flex; gap: 10px; justify-content: flex-end; }
        .btn { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; text-decoration: none; display: inline-block; transition: background 0.3s; }
        .btn-print { background-color: #2196F3; color: white; }
        .btn-print:hover { background-color: #1976D2; }
        .btn-back { background-color: #9e9e9e; color: white; }
        .btn-back:hover { background-color: #757575; }

        @media print {
            .no-print { display: none !important; }
            body { background-color: white; padding: 0; }
            .container { box-shadow: none; max-width: 100%; padding: 0; }
            @page { margin: 1cm; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="no-print">
            <button onclick="window.print()" class="btn btn-print">Cetak Sekarang</button>
            <button onclick="window.close()" class="btn btn-back">Tutup</button>
        </div>

        <div class="header">
            <h1>Laporan Penjualan</h1>
            <p>Periode: {{ \Carbon\Carbon::parse($tgl_awal)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($tgl_akhir)->format('d/m/Y') }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th width="30" class="text-center">No</th>
                    <th>Tanggal</th>
                    <th>Nota</th>
                    <th>Pelanggan</th>
                    <th>Kasir</th>
                    <th>Metode</th>
                    <th class="text-right">Total</th>
                    <th class="text-right">Diskon</th>
                    <th class="text-right">Pajak</th>
                    <th class="text-right">Total Akhir</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $total_omzet = 0; 
                    $total_diskon = 0;
                    $total_pajak = 0;
                    $total_akhir_semua = 0;
                @endphp
                @forelse($penjualans as $index => $row)
                    @php
                        $total_omzet += $row->subtotal;
                        $total_diskon += $row->diskon;
                        $total_pajak += $row->pajak_nominal;
                        $total_akhir_semua += $row->total_akhir;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($row->waktu_transaksi)->format('d/m/Y H:i') }}</td>
                        <td>{{ $row->kode_penjualan }}</td>
                        <td>{{ $row->pelanggan_nama ?? 'Umum' }}</td>
                        <td>{{ $row->kasir_nama }}</td>
                        <td>{{ $row->metode_pembayaran }}</td>
                        <td class="text-right">{{ number_format($row->subtotal, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($row->diskon, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($row->pajak_nominal, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($row->total_akhir, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">Tidak ada data untuk periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($penjualans->count() > 0)
        <div class="total-section">
            <table>
                <tr>
                    <td>Total Penjualan:</td>
                    <td class="text-right">Rp {{ number_format($total_omzet, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Total Diskon:</td>
                    <td class="text-right">Rp {{ number_format($total_diskon, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Total Pajak:</td>
                    <td class="text-right">Rp {{ number_format($total_pajak, 0, ',', '.') }}</td>
                </tr>
                <tr class="grand-total">
                    <td>TOTAL AKHIR:</td>
                    <td class="text-right">Rp {{ number_format($total_akhir_semua, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
        <div style="clear: both;"></div>
        @endif

        <div style="margin-top: 50px; font-size: 10px; color: #999;" class="text-center">
            Dicetak pada: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}
        </div>
    </div>
</body>
</html>
