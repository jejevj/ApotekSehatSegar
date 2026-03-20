@extends('layouts.app')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card" style="border-radius: 10px;">
            <div class="header">
                <h2>TAMBAH ROLE</h2>
            </div>
            <div class="body">
                <form id="role-form" action="{{ route('role.store') }}" method="POST">
                    @csrf
                    <label for="name">Nama Role</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" name="name" class="form-control" placeholder="Masukkan Nama Role" required />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <label for="feature-filter">Filter Berdasarkan Fitur</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <select id="feature-filter" class="form-control show-tick" data-container="body">
                                        <option value="">-- Semua Fitur --</option>
                                        @foreach($features as $feature)
                                            <option value="{{ $feature }}">{{ strtoupper($feature) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <label>Hak Akses (Permissions)</label>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover dataTable" id="permission-table" style="width: 100%">
                            <thead>
                                <tr>
                                    <th width="5%"><input type="checkbox" id="check-all" class="filled-in chk-col-pink"><label for="check-all"></label></th>
                                    <th width="5%">No</th>
                                    <th>Feature</th>
                                    <th>Permission Name</th>
                                    <th>Slug</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <div id="hidden-permissions"></div>

                    <div class="m-t-20">
                        <button type="submit" class="btn btn-primary waves-effect">Simpan</button>
                        <a href="{{ route('role.index') }}" class="btn btn-default waves-effect">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    var selectedPermissions = [];

    var table = $('#permission-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('role.permissionData') }}',
            data: function (d) {
                d.feature = $('#feature-filter').val();
            }
        },
        pageLength: 25,
        columns: [
            { 
                data: 'checkbox', 
                name: 'checkbox', 
                orderable: false, 
                searchable: false,
                render: function(data, type, row) {
                    var isChecked = selectedPermissions.includes(row.id) ? 'checked' : '';
                    return '<div class="demo-checkbox">' +
                                '<input type="checkbox" id="perm_' + row.id + '" value="' + row.id + '" class="filled-in chk-col-pink perm-checkbox" ' + isChecked + '>' +
                                '<label for="perm_' + row.id + '"></label>' +
                            '</div>';
                }
            },
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'feature', name: 'feature' },
            { data: 'name', name: 'name' },
            { data: 'slug', name: 'slug' }
        ],
        drawCallback: function() {
            updateCheckAllState();
        }
    });

    // Handle filter change
    $('#feature-filter').on('change', function() {
        table.draw();
    });

    // Handle individual checkbox click
    $('#permission-table').on('change', '.perm-checkbox', function() {
        var id = parseInt($(this).val());
        if ($(this).is(':checked')) {
            if (!selectedPermissions.includes(id)) {
                selectedPermissions.push(id);
            }
        } else {
            selectedPermissions = selectedPermissions.filter(function(val) {
                return val !== id;
            });
        }
        updateCheckAllState();
    });

    // Handle "check all" checkbox click
    $('#check-all').on('change', function() {
        var isChecked = $(this).is(':checked');
        $('.perm-checkbox').each(function() {
            $(this).prop('checked', isChecked).trigger('change');
        });
    });

    function updateCheckAllState() {
        var allChecked = true;
        var checkboxes = $('.perm-checkbox');
        if (checkboxes.length === 0) {
            allChecked = false;
        } else {
            checkboxes.each(function() {
                if (!$(this).is(':checked')) {
                    allChecked = false;
                    return false;
                }
            });
        }
        $('#check-all').prop('checked', allChecked);
    }

    // On form submit, add selected permissions as hidden inputs
    $('#role-form').on('submit', function() {
        var container = $('#hidden-permissions');
        container.empty();
        selectedPermissions.forEach(function(id) {
            container.append('<input type="hidden" name="permissions[]" value="' + id + '">');
        });
        return true;
    });
});
</script>
@endpush
