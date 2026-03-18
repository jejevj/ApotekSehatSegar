@extends('layouts.app')

@push('styles')
<link href="{{ asset('plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" />
@endpush

@section('content')
<div class="container-fluid">
    <div class="block-header">
        <h2>TAMBAH BARANG MASUK</h2>
    </div>

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>FORM BARANG MASUK</h2>
                </div>
                <div class="body">
                    <form action="{{ route('pembelian.store') }}" method="POST" enctype="multipart/form-data" id="formPembelian">
                        @csrf
                        <div class="row clearfix">
                            <div class="col-md-4">
                                <label for="no_faktur">No Faktur / Invoice</label>
                                <div class="form-group">
                                    <div class="form-line">
                                        <input type="text" id="no_faktur" name="no_faktur" class="form-control" placeholder="Masukkan no faktur" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="tanggal">Tanggal Masuk</label>
                                <div class="form-group">
                                    <div class="form-line">
                                        <input type="date" id="tanggal" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="distributor_id">Distributor / Agen</label>
                                <div class="form-group">
                                    <select name="distributor_id" id="distributor_id" class="form-control show-tick" data-live-search="true" data-container="body" data-width="100%" data-dropup-auto="false" required>
                                        <option value="">-- Pilih Distributor --</option>
                                        @foreach($distributors as $dist)
                                            <option value="{{ $dist->id }}">{{ $dist->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <h4 class="m-t-20 m-b-20">Daftar Barang</h4>
                        
                        <div class="row clearfix m-b-20">
                            <div class="col-md-6">
                                <select id="pilih_barang" class="form-control show-tick" data-live-search="true" data-container="body" data-width="100%" data-dropup-auto="false">
                                    <option value="">-- Cari Barang untuk Ditambahkan --</option>
                                    @foreach($barangs as $brg)
                                        <option value="{{ $brg->kode_barcode }}" data-nama="{{ $brg->nama_barang }}" data-harga="{{ $brg->harga_beli }}">
                                            {{ $brg->kode_barcode }} - {{ $brg->nama_barang }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="button" id="btnTambahBarang" class="btn btn-primary waves-effect">TAMBAH ITEM</button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="tabelItems">
                                <thead>
                                    <tr>
                                        <th>Kode Barcode</th>
                                        <th>Nama Barang</th>
                                        <th width="15%">Harga Beli (Rp)</th>
                                        <th width="10%">Jumlah</th>
                                        <th width="15%">Subtotal (Rp)</th>
                                        <th width="5%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Items will be added here -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-right">TOTAL KESELURUHAN</th>
                                        <th id="totalKeseluruhanText" class="text-right">0</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="row clearfix m-t-20">
                            <div class="col-md-4">
                                <label for="dibayar">Nominal Dibayar (Rp)</label>
                                <div class="form-group">
                                    <div class="form-line">
                                        <input type="number" id="dibayar" name="dibayar" class="form-control" value="0" min="0" required>
                                    </div>
                                    <small class="help-block">Isi 0 jika hutang penuh. Isi sesuai total jika lunas.</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="bukti_nota">Upload Bukti Nota (Opsional)</label>
                                <div class="form-group">
                                    <div class="form-line">
                                        <input type="file" id="bukti_nota" name="bukti_nota" class="form-control" accept="image/*">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success m-t-15 waves-effect" id="btnSimpan">SIMPAN BARANG MASUK</button>
                        <a href="{{ route('pembelian.index') }}" class="btn btn-default m-t-15 waves-effect">BATAL</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('plugins/bootstrap-select/js/bootstrap-select.js') }}"></script>
<script>
$(function() {
    let itemIndex = 0;

    if ($.fn.selectpicker) {
        $('#distributor_id').selectpicker('destroy').selectpicker({
            container: 'body',
            liveSearch: true,
            width: '100%'
        });
        $('#pilih_barang').selectpicker('destroy').selectpicker({
            container: 'body',
            liveSearch: true,
            width: '100%'
        });
    }

    function hitungTotal() {
        let total = 0;
        $('.subtotal-input').each(function() {
            total += parseInt($(this).val() || 0);
        });
        $('#totalKeseluruhanText').text(total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
    }

    $('#btnTambahBarang').click(function() {
        let selected = $('#pilih_barang').find(':selected');
        let barcode = selected.val();
        
        if (!barcode) {
            alert('Pilih barang terlebih dahulu!');
            return;
        }

        // Cek apakah barang sudah ada di tabel
        let exists = false;
        $('.barcode-input').each(function() {
            if ($(this).val() === barcode) {
                exists = true;
            }
        });

        if (exists) {
            alert('Barang sudah ada di daftar, silakan ubah jumlahnya.');
            return;
        }

        let nama = selected.data('nama');
        let harga = selected.data('harga');

        let tr = `
            <tr>
                <td>
                    <input type="hidden" name="items[${itemIndex}][kode_barcode]" class="barcode-input" value="${barcode}">
                    ${barcode}
                </td>
                <td>${nama}</td>
                <td>
                    <div class="form-line">
                        <input type="number" name="items[${itemIndex}][harga_beli]" class="form-control harga-input" value="${harga}" min="0" required>
                    </div>
                </td>
                <td>
                    <div class="form-line">
                        <input type="number" name="items[${itemIndex}][jumlah]" class="form-control jumlah-input" value="1" min="1" required>
                    </div>
                </td>
                <td>
                    <input type="hidden" class="subtotal-input" value="${harga}">
                    <span class="subtotal-text">${harga.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")}</span>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-xs btn-hapus-item"><i class="material-icons">delete</i></button>
                </td>
            </tr>
        `;

        $('#tabelItems tbody').append(tr);
        itemIndex++;
        hitungTotal();
        
        // Reset select
        $('#pilih_barang').val('').selectpicker('refresh');
    });

    $(document).on('input', '.harga-input, .jumlah-input', function() {
        let tr = $(this).closest('tr');
        let harga = parseInt(tr.find('.harga-input').val() || 0);
        let jumlah = parseInt(tr.find('.jumlah-input').val() || 0);
        let subtotal = harga * jumlah;
        
        tr.find('.subtotal-input').val(subtotal);
        tr.find('.subtotal-text').text(subtotal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
        
        hitungTotal();
    });

    $(document).on('click', '.btn-hapus-item', function() {
        $(this).closest('tr').remove();
        hitungTotal();
    });

    $('#formPembelian').submit(function(e) {
        if ($('#tabelItems tbody tr').length === 0) {
            e.preventDefault();
            alert('Daftar barang tidak boleh kosong!');
        }
    });
});
</script>
@endpush
