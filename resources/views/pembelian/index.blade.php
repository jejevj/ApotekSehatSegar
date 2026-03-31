@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="block-header">
        <h2>DATA {{ strtoupper(label('product')) }} MASUK ({{ strtoupper(label('purchase')) }})</h2>
    </div>

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        DAFTAR {{ strtoupper(label('product')) }} MASUK
                    </h2>
                    @if(auth()->user()->hasPermission('pembelian.create'))
                    <ul class="header-dropdown m-r--5">
                        <li>
                            <a href="{{ route('pembelian.create') }}" class="btn btn-primary waves-effect" style="color: white;">
                                <i class="material-icons">add</i>
                                <span>TAMBAH {{ strtoupper(label('product')) }} MASUK</span>
                            </a>
                        </li>
                    </ul>
                    @endif
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover dataTable js-pembelian-table">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>No Faktur</th>
                                    <th>Tanggal</th>
                                    <th>{{ label('supplier') }}</th>
                                    <th>Total</th>
                                    <th>Sisa Tagihan</th>
                                    <th>Status</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    $('.js-pembelian-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('pembelian.data') }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'no_faktur', name: 'no_faktur' },
            { data: 'tanggal', name: 'tanggal' },
            { data: 'distributor_nama', name: 'distributor_nama', orderable: false },
            { data: 'total', name: 'total', className: 'text-right' },
            { data: 'sisa', name: 'sisa', className: 'text-right' },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false, className: 'text-center' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-center' }
        ]
    });
});
</script>
@endpush