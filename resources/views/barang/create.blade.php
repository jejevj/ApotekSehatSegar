@extends('layouts.app')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card" style="border-radius: 10px;">
            <div class="header">
                <h2>{{ 'TAMBAH ' . strtoupper(label('product')) }}</h2>
            </div>
            <div class="body">
                <form action="{{ route('barang.store') }}" method="POST">
                    @csrf
                    <label for="kode_barcode">Barcode</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" name="kode_barcode" class="form-control" placeholder="Masukkan Barcode" required />
                        </div>
                    </div>

                    <label for="nama_barang">{{ 'Nama ' . label('product') }}</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" name="nama_barang" class="form-control" placeholder="Masukkan Nama Barang" required />
                        </div>
                    </div>

                    <label for="category_id">Kategori</label>
                    <div class="input-group">
                        <div class="form-line">
                            <select name="category_id" id="category_id" class="form-control show-tick" data-container="body" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $category->id == 1 ? 'selected' : '' }}>{{ $category->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                        <span class="input-group-addon">
                            <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#addCategoryModal">
                                <i class="material-icons">add</i>
                            </button>
                        </span>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="unit_id">Satuan</label>
                            <div class="input-group">
                                <div class="form-line">
                                    <select name="unit_id" id="unit_id" class="form-control show-tick" data-container="body" required>
                                        <option value="">-- Pilih Satuan --</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span class="input-group-addon">
                                    <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#addUnitModal">
                                        <i class="material-icons">add</i>
                                    </button>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            @if($businessConfig->showProductLocation())
                            <label for="rak_id">{{ label('location') }}</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <select name="rak_id" id="rak_id" class="form-control show-tick" data-container="body">
                                        <option value="">-- Belum Terorganisir --</option>
                                        @foreach($raks as $rak)
                                            <option value="{{ $rak->id }}">{{ $rak->nama_lokasi }} - {{ $rak->nama_rak }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            @if($businessConfig->showProductContent())
                            <label for="isi">Isi (Opsional)</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="number" name="isi" id="isi" class="form-control" value="1" min="1" />
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <label for="harga_beli">Harga Beli</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="number" name="harga_beli" class="form-control" placeholder="Masukkan Harga Beli" required />
                        </div>
                    </div>

                    <label for="stok">Stok</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="number" name="stok" class="form-control" placeholder="Masukkan Stok" required />
                        </div>
                    </div>

                    <label for="harga_jual">Harga Jual</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="number" name="harga_jual" class="form-control" placeholder="Masukkan Harga Jual" required />
                        </div>
                    </div>

                    <input type="submit" name="simpan" value="Simpan" class="btn btn-primary">
                    <a href="{{ route('barang.index') }}" class="btn btn-default">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Kategori Baru</h4>
            </div>
            <div class="modal-body">
                <form id="form-add-category">
                    <label for="new_category_name">Nama Kategori</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" id="new_category_name" class="form-control" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn-save-category" class="btn btn-primary waves-effect">Simpan</button>
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Unit Modal -->
<div class="modal fade" id="addUnitModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Satuan Baru</h4>
            </div>
            <div class="modal-body">
                <form id="form-add-unit">
                    <label for="new_unit_name">Nama Satuan</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" id="new_unit_name" class="form-control" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn-save-unit" class="btn btn-primary waves-effect">Simpan</button>
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    // Save Category
    $('#btn-save-category').on('click', function() {
        var categoryName = $('#new_category_name').val();
        if (!categoryName) {
            swal("Error", "Nama kategori tidak boleh kosong", "error");
            return;
        }

        $.ajax({
            url: '{{ route("category.store") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                nama_kategori: categoryName
            },
            success: function(response) {
                if (response.success) {
                    var newOption = new Option(response.category.nama_kategori, response.category.id, true, true);
                    $('#category_id').append(newOption).trigger('change');
                    $('#category_id').selectpicker('refresh');
                    $('#addCategoryModal').modal('hide');
                    $('#new_category_name').val('');
                    swal("Berhasil", "Kategori baru berhasil ditambahkan", "success");
                } else {
                    swal("Error", response.message || "Gagal menambahkan kategori", "error");
                }
            },
            error: function(xhr) {
                var errorMsg = "Terjadi kesalahan";
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors).join('\n');
                }
                swal("Error", errorMsg, "error");
            }
        });
    });

    // Save Unit
    $('#btn-save-unit').on('click', function() {
        var unitName = $('#new_unit_name').val();
        if (!unitName) {
            swal("Error", "Nama satuan tidak boleh kosong", "error");
            return;
        }

        $.ajax({
            url: '{{ route("unit.store") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                nama: unitName
            },
            success: function(response) {
                if (response.success) {
                    var newOption = new Option(response.unit.nama, response.unit.id, true, true);
                    $('#unit_id').append(newOption).trigger('change');
                    $('#unit_id').selectpicker('refresh');
                    $('#addUnitModal').modal('hide');
                    $('#new_unit_name').val('');
                    swal("Berhasil", "Satuan baru berhasil ditambahkan", "success");
                } else {
                    swal("Error", response.message || "Gagal menambahkan satuan", "error");
                }
            },
            error: function(xhr) {
                var errorMsg = "Terjadi kesalahan";
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors).join('\n');
                }
                swal("Error", errorMsg, "error");
            }
        });
    });
});
</script>
@endpush
