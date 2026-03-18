@extends('layouts.app')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card" style="border-radius: 10px;">
            <div class="header">
                <h2>DAFTAR BILLING</h2>
                <a href="{{ route('billing.create') }}" class="btn btn-primary"><i class="material-icons">add</i></a>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover dataTable js-billing-table">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="20%">Jatuh Tempo</th>
                                <th width="15%">Jumlah Tagihan</th>
                                <th>Bank</th>
                                <th>No. Rekening</th>
                                <th width="10%">Status</th>
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
    $('.js-billing-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('billing.data') }}',
        order: [[1, 'desc']],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'expired_at_fmt', name: 'expired_at' },
            { data: 'jumlah_tagihan', name: 'jumlah_tagihan', render: function(data) {
                if (!data) return '-';
                return 'Rp ' + parseInt(data).toLocaleString('id-ID');
            }},
            { data: 'nama_bank', name: 'nama_bank' },
            { data: 'no_rek', name: 'no_rek' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
        ]
    });
});
</script>
@endpush

