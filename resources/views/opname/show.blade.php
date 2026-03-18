@extends('layouts.app')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card" style="border-radius: 10px;">
            <div class="header">
                <h2>DETAIL OPNAME: {{ $opname->kode_opname }}</h2>
            </div>
            <div class="body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Tanggal:</strong> {{ $opname->tanggal->translatedFormat('d F Y H:i') }}
                    </div>
                    <div class="col-md-3">
                        <strong>Status:</strong> 
                        @if ($opname->status === 'draft') <span class="label bg-amber">Draft</span>
                        @elseif ($opname->status === 'menunggu_approve') <span class="label bg-orange">Menunggu Approve</span>
                        @elseif ($opname->status === 'selesai') <span class="label bg-green">Selesai</span>
                        @elseif ($opname->status === 'dibatalkan') <span class="label bg-red">Dibatalkan</span>
                        @endif
                    </div>
                    <div class="col-md-3">
                        <strong>Input Oleh:</strong> {{ $opname->user?->nama ?? '-' }}
                    </div>
                    @if ($opname->status === 'selesai')
                    <div class="col-md-3">
                        <strong>Diverifikasi Oleh:</strong> {{ $opname->approvedBy?->nama ?? '-' }} <br>
                        <small>({{ $opname->approved_at?->translatedFormat('d F Y H:i') }})</small>
                    </div>
                    @endif
                </div>
                @if($opname->catatan)
                <div class="m-t-10">
                    <strong>Catatan:</strong> {{ $opname->catatan }}
                </div>
                @endif

                @if($opname->status !== 'draft')
                <div class="row m-t-20">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="info-box bg-red hover-expand-effect">
                            <div class="icon">
                                <i class="material-icons">trending_down</i>
                            </div>
                            <div class="content">
                                <div class="text">TOTAL KERUGIAN ({{ $opname->items->where('selisih', '<', 0)->count() }} Jenis Barang)</div>
                                <div class="number">Rp {{ number_format($opname->total_nilai_rugi, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="info-box bg-green hover-expand-effect">
                            <div class="icon">
                                <i class="material-icons">trending_up</i>
                            </div>
                            <div class="content">
                                <div class="text">TOTAL KELEBIHAN ({{ $opname->items->where('selisih', '>', 0)->count() }} Jenis Barang)</div>
                                <div class="number">Rp {{ number_format($opname->total_nilai_lebih, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="table-responsive m-t-20">
                    <table class="table table-bordered table-striped table-hover dataTable js-opname-items-table">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Barcode</th>
                                <th>Nama Barang</th>
                                <th width="12%">Harga Beli</th>
                                <th width="10%">Stok Sistem</th>
                                <th width="10%">Stok Fisik</th>
                                <th width="10%">Selisih</th>
                                <th width="12%">Nilai Selisih</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <div class="m-t-20">
                    <a href="{{ route('opname.index') }}" class="btn btn-default waves-effect">Kembali</a>
                    
                    @if($opname->status === 'draft' && auth()->user()->hasPermission('opname.update'))
                        <a href="{{ route('opname.edit', $opname->id) }}" class="btn btn-primary waves-effect">Edit</a>
                    @endif

                    @if($opname->status === 'menunggu_approve' && auth()->user()->hasPermission('opname.approve'))
                        <form action="{{ route('opname.approve', $opname->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Setujui opname ini dan sesuaikan stok?')">
                            @csrf
                            <button type="submit" class="btn btn-success waves-effect">Setujui & Sesuaikan Stok</button>
                        </form>
                        <form action="{{ route('opname.reject', $opname->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Kembalikan opname ke draft?')">
                            @csrf
                            <button type="submit" class="btn btn-warning waves-effect">Kembalikan ke Draft</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    $('.js-opname-items-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("opname.dataItems", $opname->id) }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'kode_barcode', name: 'kode_barcode' },
            { data: 'nama_barang', name: 'nama_barang' },
            { data: 'harga_beli_rp', name: 'harga_beli', className: 'text-right' },
            { data: 'stok_sistem', name: 'stok_sistem', className: 'text-center' },
            { data: 'stok_fisik', name: 'stok_fisik', className: 'text-center' },
            { data: 'selisih', name: 'selisih', className: 'text-center' },
            { data: 'nilai_selisih_rp', name: 'nilai_selisih', className: 'text-right', orderable: false, searchable: false },
        ]
    });
});
</script>
@endpush

