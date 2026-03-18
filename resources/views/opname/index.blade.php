@extends('layouts.app')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card" style="border-radius: 10px;">
            <div class="header">
                <h2>OPNAME STOK</h2>
                @if(auth()->user()->hasPermission('opname.create'))
                <a href="{{ route('opname.create') }}" class="btn btn-primary"><i class="material-icons">add</i></a>
                @endif
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover dataTable js-opname-table">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Kode</th>
                                <th>Tanggal</th>
                                <th>User</th>
                                <th>Total Rugi</th>
                                <th>Total Lebih</th>
                                <th>Status</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    $('.js-opname-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('opname.data') }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'kode_opname', name: 'kode_opname' },
            { data: 'tanggal', name: 'tanggal' },
            { data: 'user_nama', name: 'user_nama', orderable: false },
            { data: 'nilai_rugi', name: 'total_nilai_rugi', className: 'text-right' },
            { data: 'nilai_lebih', name: 'total_nilai_lebih', className: 'text-right' },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
        ]
    });
});
</script>
@endpush

