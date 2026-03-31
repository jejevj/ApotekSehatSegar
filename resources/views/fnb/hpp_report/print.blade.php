<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan HPP & Margin</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        h2 { text-align: center; margin-bottom: 4px; }
        .subtitle { text-align: center; color: #555; margin-bottom: 16px; font-size: 11px; }
        .summary { display: flex; gap: 20px; margin-bottom: 16px; }
        .summary-item { border: 1px solid #ccc; padding: 8px 14px; border-radius: 4px; flex: 1; text-align: center; }
        .summary-item .label { font-size: 10px; color: #666; text-transform: uppercase; }
        .summary-item .value { font-size: 14px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 5px 8px; }
        th { background: #f0f0f0; text-align: center; }
        td.right { text-align: right; }
        td.center { text-align: center; }
        .text-green { color: #2e7d32; }
        .text-red { color: #c62828; }
        .text-muted { color: #999; }
        @media print {
            body { margin: 10px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <h2>Laporan HPP & Margin</h2>
    <div class="subtitle">Dicetak pada: {{ now()->format('d/m/Y H:i') }}</div>

    <div class="summary">
        <div class="summary-item">
            <div class="label">Total Menu</div>
            <div class="value">{{ $data['total_menu'] }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Rata-rata HPP</div>
            <div class="value">Rp {{ number_format($data['avg_hpp'], 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Rata-rata Margin</div>
            <div class="value">{{ $data['avg_margin'] }}%</div>
        </div>
        <div class="summary-item">
            <div class="label">Belum Ada Resep</div>
            <div class="value">{{ $data['menu_tanpa_resep'] }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nama Menu</th>
                <th>Kategori</th>
                <th>Resep</th>
                <th>HPP/Porsi</th>
                <th>Harga Jual</th>
                <th>Margin (Rp)</th>
                <th>Margin (%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['rows'] as $i => $row)
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td>{{ $row['nama_barang'] }}</td>
                <td>{{ $row['kategori'] }}</td>
                <td>{{ $row['resep_nama'] ?? '-' }}</td>
                <td class="right">
                    @if($row['hpp'] !== null)
                        Rp {{ number_format($row['hpp'], 0, ',', '.') }}
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td class="right">Rp {{ number_format($row['harga_jual'], 0, ',', '.') }}</td>
                <td class="right">
                    @if($row['margin_nominal'] !== null)
                        <span class="{{ $row['margin_nominal'] >= 0 ? 'text-green' : 'text-red' }}">
                            Rp {{ number_format($row['margin_nominal'], 0, ',', '.') }}
                        </span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td class="right">
                    @if($row['margin_persen'] !== null)
                        <span class="{{ $row['margin_persen'] >= 0 ? 'text-green' : 'text-red' }}">
                            {{ $row['margin_persen'] }}%
                        </span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <script>window.onload = function() { window.print(); }</script>
</body>
</html>
