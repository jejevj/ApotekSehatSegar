@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="block-header">
        <h2>DATA DISTRIBUTOR</h2>
    </div>

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        DAFTAR DISTRIBUTOR
                    </h2>
                    @if(auth()->user()->hasPermission('distributor.create'))
                    <ul class="header-dropdown m-r--5">
                        <li>
                            <a href="{{ route('distributor.create') }}" class="btn btn-primary waves-effect" style="color: white;">
                                <i class="material-icons">add</i>
                                <span>TAMBAH DISTRIBUTOR</span>
                            </a>
                        </li>
                    </ul>
                    @endif
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover dataTable js-distributor-table">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama</th>
                                    <th>Telepon</th>
                                    <th>Alamat</th>
                                    <th>Keterangan</th>
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
    $('.js-distributor-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('distributor.data') }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nama', name: 'nama' },
            { data: 'telepon', name: 'telepon' },
            { data: 'alamat', name: 'alamat' },
            { data: 'keterangan', name: 'keterangan' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-center' }
        ]
    });
});
</script>
@endpush