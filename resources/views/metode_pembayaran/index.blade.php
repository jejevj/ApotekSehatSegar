@extends('layouts.app')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card" style="border-radius: 10px;">
            <div class="header">
                <h2>METODE PEMBAYARAN</h2>
            </div>
            <div class="body">
                <a href="{{ route('metode_pembayaran.create') }}" class="btn btn-primary" style="margin-bottom: 15px;">Tambah Metode Pembayaran</a>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover dataTable" id="metode-pembayaran-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Kode</th>
                                <th>Tipe</th>
                                <th>Nama Bank</th>
                                <th>No. Rekening</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    $(function () {
        $('#metode-pembayaran-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("metode_pembayaran.data") }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nama', name: 'nama' },
                { data: 'kode', name: 'kode' },
                { data: 'tipe', name: 'tipe' },
                { data: 'nama_bank', name: 'nama_bank' },
                { data: 'no_rekening', name: 'no_rekening' },
                { data: 'is_aktif', name: 'is_aktif' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ]
        });
    });
</script>
@endpush

