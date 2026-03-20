@extends('layouts.app')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card" style="border-radius: 10px;">
            <div class="header">
                <h2>DATA MENU</h2>
                <div class="pull-right">
                    <button type="button" id="btn-save-order" class="btn btn-success" style="display: none; border-radius: 5px; margin-right: 8px;">
                        <i class="material-icons">save</i> Simpan Urutan
                    </button>
                    @if(auth()->user()->hasPermission('menu.create'))
                    <a href="{{ route('menu.create') }}" class="btn btn-primary" style="border-radius: 5px;"><i class="material-icons">add</i></a>
                    @endif
                </div>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover dataTable js-menu-table">
                        <thead>
                            <tr>
                                <th width="30"></th>
                                <th width="5%">No</th>
                                <th>Nama Menu</th>
                                <th>Icon</th>
                                <th>Route/URL</th>
                                <th>Parent</th>
                                <th>Urutan</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="menu-tbody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script>
$(function() {
    var table = $('.js-menu-table').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 100,
        ajax: '{{ route('menu.data') }}',
        columns: [
            { data: 'drag_handle', name: 'drag_handle', orderable: false, searchable: false },
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'icon', name: 'icon', render: function(data) {
                return '<i class="material-icons">'+data+'</i>';
            }},
            { data: 'route_name', name: 'route_name', render: function(data, type, row) {
                return data ? data : row.url;
            }},
            { data: 'parent_name', name: 'parent_name' },
            { data: 'order', name: 'order' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
        ],
        createdRow: function(row, data, dataIndex) {
            $(row).attr('data-id', data.id);
            $(row).addClass('sortable-row');
        },
        drawCallback: function() {
            initSortable();
        }
    });

    function initSortable() {
        $("#menu-tbody").sortable({
            handle: ".drag-handle",
            axis: "y",
            update: function(event, ui) {
                $('#btn-save-order').show();
                // Update "Urutan" column locally for visual feedback
                $("#menu-tbody tr").each(function(index) {
                    $(this).find('td:eq(6)').text(index + 1);
                });
            }
        }).disableSelection();
    }

    $('#btn-save-order').on('click', function() {
        var orders = [];
        $("#menu-tbody tr").each(function(index) {
            orders.push({
                id: $(this).data('id'),
                order: index + 1
            });
        });

        $.ajax({
            url: '{{ route('menu.updateOrder') }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                orders: orders
            },
            success: function(response) {
                swal("Berhasil!", response.success, "success").then(function() {
                    location.reload();
                });
            },
            error: function(xhr) {
                swal("Gagal!", "Terjadi kesalahan saat menyimpan urutan.", "error");
            }
        });
    });
});
</script>
@endpush
