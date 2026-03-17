<h1>Laporan Penjualan</h1>
<p>Periode: {{ $tgl_awal }} - {{ $tgl_akhir }}</p>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Nama Pelanggan</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($penjualans as $penjualan)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $penjualan->tanggal }}</td>
            <td>{{ $penjualan->pelanggan->nama_pelanggan }}</td>
            <td>{{ $penjualan->total }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
