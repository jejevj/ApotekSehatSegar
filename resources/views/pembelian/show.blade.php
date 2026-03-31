@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="block-header">
        <h2>DETAIL {{ strtoupper(label('product')) }} MASUK</h2>
    </div>

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>INFORMASI FAKTUR: {{ $pembelian->no_faktur }}</h2>
                </div>
                <div class="body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Tanggal:</strong><br>
                            {{ $pembelian->tanggal->translatedFormat('d F Y') }}
                        </div>
                        <div class="col-md-3">
                            <strong>{{ label('supplier') }}:</strong><br>
                            {{ $pembelian->distributor->nama ?? '-' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Status:</strong><br>
                            @if($pembelian->status === 'lunas')
                                <span class="label bg-green">Lunas</span>
                            @else
                                <span class="label bg-red">Belum Lunas</span>
                            @endif
                        </div>
                        <div class="col-md-3">
                            <strong>Diinput Oleh:</strong><br>
                            {{ $pembelian->user->nama ?? '-' }}
                        </div>
                    </div>

                    @if($pembelian->bukti_nota)
                    <div class="row m-t-20">
                        <div class="col-md-12">
                            <strong>Bukti Nota:</strong><br>
                            <a href="{{ asset('storage/' . $pembelian->bukti_nota) }}" target="_blank" class="btn btn-xs btn-info">Lihat Bukti Nota</a>
                        </div>
                    </div>
                    @endif

                    <h4 class="m-t-30">Daftar {{ label('product') }}</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Kode Barcode</th>
                                    <th>Nama {{ label('product') }}</th>
                                    <th class="text-right">Harga Beli</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pembelian->details as $i => $detail)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $detail->kode_barcode }}</td>
                                    <td>{{ $detail->nama_barang }}</td>
                                    <td class="text-right">Rp {{ number_format($detail->harga_beli, 0, ',', '.') }}</td>
                                    <td class="text-center">{{ $detail->jumlah }}</td>
                                    <td class="text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5" class="text-right">TOTAL KESELURUHAN</th>
                                    <th class="text-right">Rp {{ number_format($pembelian->total, 0, ',', '.') }}</th>
                                </tr>
                                <tr>
                                    <th colspan="5" class="text-right">TOTAL DIBAYAR</th>
                                    <th class="text-right text-success">Rp {{ number_format($pembelian->dibayar, 0, ',', '.') }}</th>
                                </tr>
                                <tr>
                                    <th colspan="5" class="text-right">SISA TAGIHAN</th>
                                    <th class="text-right text-danger">Rp {{ number_format($pembelian->sisa, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <h4 class="m-t-30">Riwayat Pembayaran</h4>
                    @if($pembelian->status === 'belum_lunas' && auth()->user()->hasPermission('pembelian.update'))
                        <button type="button" class="btn btn-primary waves-effect m-b-15" data-toggle="modal" data-target="#modalBayar">
                            TAMBAH PEMBAYARAN
                        </button>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Tanggal Bayar</th>
                                    <th>Nominal</th>
                                    <th>Metode</th>
                                    <th>Keterangan</th>
                                    <th>Bukti</th>
                                    <th>Kasir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pembelian->pembayarans as $bayar)
                                <tr>
                                    <td>{{ $bayar->tanggal_bayar->translatedFormat('d F Y') }}</td>
                                    <td>Rp {{ number_format($bayar->nominal, 0, ',', '.') }}</td>
                                    <td>{{ $bayar->metode_pembayaran }}</td>
                                    <td>{{ $bayar->keterangan ?? '-' }}</td>
                                    <td>
                                        @if($bayar->bukti_bayar)
                                            <a href="{{ asset('storage/' . $bayar->bukti_bayar) }}" target="_blank">Lihat</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $bayar->user->nama ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada riwayat pembayaran</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="m-t-20">
                        <a href="{{ route('pembelian.index') }}" class="btn btn-default waves-effect">KEMBALI</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Pembayaran -->
<div class="modal fade" id="modalBayar" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Pembayaran Cicilan</h4>
            </div>
            <form action="{{ route('pembelian.bayar', $pembelian->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        Sisa Tagihan: <strong>Rp {{ number_format($pembelian->sisa, 0, ',', '.') }}</strong>
                    </div>

                    <label for="tanggal_bayar">Tanggal Bayar</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="date" name="tanggal_bayar" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <label for="nominal">Nominal Pembayaran (Rp)</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="number" name="nominal" class="form-control" max="{{ $pembelian->sisa }}" required>
                        </div>
                    </div>

                    <label for="metode_pembayaran">Metode Pembayaran</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" name="metode_pembayaran" class="form-control" placeholder="Contoh: Transfer BCA, Tunai" required>
                        </div>
                    </div>

                    <label for="keterangan">Keterangan</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" name="keterangan" class="form-control" placeholder="Opsional">
                        </div>
                    </div>

                    <label for="bukti_bayar">Bukti Transfer/Bayar (Opsional)</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="file" name="bukti_bayar" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success waves-effect">SIMPAN PEMBAYARAN</button>
                    <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">BATAL</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection