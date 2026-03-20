@extends('layouts.app')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card" style="border-radius: 10px;">
            <div class="header">
                <h2>TRANSAKSI PENJUALAN</h2>
            </div>
            <div class="body">
                <div id="toast-area" style="position: fixed; top: 80px; right: 20px; z-index: 9999; width: 320px;"></div>
                {{-- Form untuk Scan Barcode --}}
                <form method="POST" action="{{ route('penjualan.addItem') }}">
                    @csrf
                    <div class="row clearfix">
                        <div class="col-md-3">
                            <label>Kode Penjualan</label>
                            <input type="text" name="kode_penjualan" value="{{ $kode_penjualan }}" class="form-control" readonly>
                        </div>
                        <div class="col-md-3">
                            <label>Kode Barcode</label>
                            <input type="text" name="kode_barcode" class="form-control" autofocus required>
                        </div>
                        <div class="col-md-4">
                            <label>&nbsp;</label><br>
                            <div role="group" aria-label="Aksi Produk">
                                <button type="submit" class="btn btn-primary" style="border-radius: 5px; margin-right: 8px;">Tambahkan</button>
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#productModal" style="border-radius: 5px;">Pilih Produk</button>
                            </div>
                        </div>
                    </div>
                </form>

                <hr>

                {{-- Form untuk Finalisasi Transaksi --}}
                <form id="finalForm" method="POST" action="{{ route('penjualan.storeDetail') }}">
                    @csrf
                    <input type="hidden" name="kode_penjualan" value="{{ $kode_penjualan }}">
                    <input type="hidden" name="action_type" id="action_type" value="save">
                    
                    <div class="row clearfix">
                        <div class="col-md-3">
                            <label>Pelanggan</label>
                            <select name="id_pelanggan" class="form-control show-tick" data-container="body" style="border-radius: 5px;">
                                @foreach($pelanggan as $p)
                                    <option value="{{ $p->kode_pelanggan }}" {{ $p->kode_pelanggan == 3 ? 'selected' : '' }}>
                                        {{ $p->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Metode Pembayaran</label>
                            <select name="metode_pembayaran_id" class="form-control show-tick" data-container="body" style="border-radius: 5px;">
                                @foreach($metode_pembayaran as $m)
                                    <option value="{{ $m->id }}">
                                        {{ $m->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive" style="margin-top: 20px;">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Barcode</th>
                                    <th>Nama Barang</th>
                                    <th>Harga Normal</th>
                                    <th>Harga Jual</th>
                                <th>Jumlah</th>
                                <th>Diskon/Item</th>
                                <th>Potongan</th>
                                <th>Total</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->kode_barcode }}</td>
                                        <td>{{ $item->barang->nama_barang ?? '-' }}</td>
                                        <td>{{ number_format($item->barang->harga_jual ?? 0, 0, ',', '.') }}</td>
                                        <td width="15%">
                                            <input type="number" 
                                                   value="{{ $item->harga_jual_kustom ?? $item->barang->harga_jual }}" 
                                                   class="form-control input-sm item-update" 
                                                   data-id="{{ $item->id }}" 
                                                   data-field="harga_jual_kustom" 
                                                   min="{{ $item->barang->harga_beli }}"
                                                   title="Harga beli: Rp. {{ number_format($item->barang->harga_beli, 0, ',', '.') }}">
                                            @if($item->harga_jual_kustom)
                                                <small class="text-info">Diubah oleh: {{ $item->pengubah->nama ?? '-' }}</small>
                                            @endif
                                        </td>
                                        <td width="10%">
                                            <input type="number" value="{{ $item->jumlah }}" class="form-control input-sm item-update" data-id="{{ $item->id }}" data-field="jumlah" min="1" max="{{ $item->barang->stok + $item->jumlah }}">
                                        </td>
                                        <td width="20%">
                                            <div class="input-group">
                                                <input type="number" 
                                                       value="{{ $item->diskon_item }}" 
                                                       class="form-control input-sm item-update" 
                                                       data-id="{{ $item->id }}" 
                                                       data-field="diskon_item" 
                                                       min="0">
                                                <div class="input-group-btn">
                                                    <select class="form-control item-update" data-id="{{ $item->id }}" data-field="diskon_tipe">
                                                        <option value="rupiah" @if($item->diskon_tipe == 'rupiah') selected @endif>Rp</option>
                                                        <option value="persen" @if($item->diskon_tipe == 'persen') selected @endif>%</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </td>
                                        <td><input type="text" value="{{ number_format($item->potongan_item, 0, ',', '.') }}" class="form-control" readonly></td>
                                        <td>{{ number_format($item->total, 0, ',', '.') }}</td>
                                        <td>
                                            <a href="#" onclick="event.preventDefault(); document.getElementById('remove-item-{{ $item->id }}').submit();" class="btn btn-danger btn-xs">
                                                <i class="material-icons">clear</i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center">Belum ada item</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="row clearfix" style="margin-top: 20px;">
                        <div class="col-md-12">
                            <div class="row">
                                <!-- Kolom Kiri: Total, Diskon, Sub Total -->
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="text-align: right; vertical-align: middle; width: 40%; background-color: #f9f9f9;">Total</th>
                                            <td>
                                                <input type="number" name="total_bayar" id="total_bayar" value="{{ $total_bayar }}" class="form-control text-right" readonly style="font-weight: bold;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="text-align: right; vertical-align: middle; background-color: #f9f9f9;">Diskon (Rp)</th>
                                            <td>
                                                <input type="number" name="diskon" id="diskon" class="form-control text-right" onkeyup="hitung()" placeholder="0">
                                                <input type="hidden" name="potongan" id="potongan">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="text-align: right; vertical-align: middle; background-color: #f9f9f9;">Sub Total</th>
                                            <td>
                                                <input type="number" name="s_total" id="s_total" class="form-control text-right" readonly style="font-weight: bold;">
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- Kolom Kanan: Pajak, Bayar, Kembali -->
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="text-align: right; vertical-align: middle; width: 40%; background-color: #f9f9f9;">Pajak</th>
                                            <td>
                                                <div class="row clearfix" style="margin: 0;">
                                                    <div class="col-xs-4" style="padding-left: 0;">
                                                        <input type="checkbox" id="pajak_aktif" name="pajak_aktif" value="1" class="filled-in chk-col-green" onchange="hitung()">
                                                        <label for="pajak_aktif" style="margin-bottom: 0; margin-top: 8px;">Aktif</label>
                                                    </div>
                                                    <div class="col-xs-8" style="padding-right: 0;">
                                                        <div class="input-group" style="margin-bottom: 5px;">
                                                            <input type="number" name="pajak_persen" id="pajak_persen" class="form-control text-right" value="0" min="0" max="100" onkeyup="hitung()">
                                                            <span class="input-group-addon">%</span>
                                                        </div>
                                                        <div class="form-group" style="margin-bottom: 5px;">
                                                            <div class="form-line">
                                                                <input type="text" name="pajak_keterangan" id="pajak_keterangan" class="form-control" placeholder="Ket. Pajak (ex: PPN)">
                                                            </div>
                                                        </div>
                                                        <div style="margin-bottom: 5px;">
                                                            <input name="pajak_ditanggung" type="radio" id="pajak_toko" value="toko" class="with-gap radio-col-blue" onchange="hitung()" checked />
                                                            <label for="pajak_toko">Toko</label>
                                                            <input name="pajak_ditanggung" type="radio" id="pajak_pembeli" value="pembeli" class="with-gap radio-col-red" onchange="hitung()" />
                                                            <label for="pajak_pembeli">Pembeli</label>
                                                        </div>
                                                        <div class="input-group" style="margin-bottom: 0;">
                                                            <span class="input-group-addon">Rp</span>
                                                            <input type="text" name="pajak_nominal" id="pajak_nominal" class="form-control text-right" value="0" readonly style="background-color: #eee;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="text-align: right; vertical-align: middle; background-color: #f9f9f9; font-size: 16px; color: #e91e63;">Total Akhir</th>
                                            <td>
                                                <input type="number" name="total_akhir" id="total_akhir" class="form-control text-right" value="0" readonly style="font-weight: bold; font-size: 18px; color: #e91e63;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="text-align: right; vertical-align: middle; background-color: #f9f9f9;">Bayar</th>
                                            <td>
                                                <input type="number" name="bayar" id="bayar" class="form-control text-right" onkeyup="hitung()" required style="font-weight: bold; font-size: 16px;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="text-align: right; vertical-align: middle; background-color: #f9f9f9;">Kembali</th>
                                            <td>
                                                <input type="number" name="kembali" id="kembali" class="form-control text-right" readonly style="font-weight: bold; font-size: 16px;">
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row clearfix" style="margin-top: 10px; border-top: 1px solid #eee; padding-top: 20px;">
                        <div class="col-md-12 text-right">
                            <button type="button" id="btn-finish" class="btn btn-info btn-lg waves-effect" style="margin-right: 8px;"><i class="material-icons">save</i> Selesaikan</button>
                            <button type="button" id="btn-finish-print" class="btn btn-success btn-lg waves-effect" style="margin-right: 8px;"><i class="material-icons">print</i> Selesaikan & Cetak</button>
                            <button type="button" id="btn-cancel" class="btn btn-danger btn-lg waves-effect"><i class="material-icons">cancel</i> Batalkan</button>
                        </div>
                    </div>
                </form>

                <form id="cancelForm" method="POST" action="{{ route('penjualan.cancel') }}" style="display: none;">
                    @csrf
                    <input type="hidden" name="kode_penjualan" value="{{ $kode_penjualan }}">
                </form>

                {{-- Form tersembunyi untuk hapus item --}}
                @foreach($items as $item)
                    <form id="remove-item-{{ $item->id }}" action="{{ route('penjualan.removeItem', $item->id) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    function showAlert(type, message) {
        var alertHtml = '<div class="alert alert-' + type + ' alert-dismissible" role="alert" style="margin-bottom: 10px;">' +
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
            message +
            '</div>';
        $('#toast-area').append(alertHtml);
        window.setTimeout(function() {
            $("#toast-area .alert").first().fadeTo(500, 0).slideUp(500, function(){
                $(this).remove();
            });
        }, 5000);
    }

    function hitung() {
        var total_bayar = parseInt(document.getElementById('total_bayar').value) || 0;
        var diskon = parseInt(document.getElementById('diskon').value) || 0;
        
        document.getElementById('potongan').value = diskon;
        
        var sub_total = total_bayar - diskon;
        document.getElementById('s_total').value = sub_total;

        var pajakAktif = document.getElementById('pajak_aktif') ? document.getElementById('pajak_aktif').checked : false;
        var pajakPersen = parseInt(document.getElementById('pajak_persen') ? document.getElementById('pajak_persen').value : 0) || 0;
        var pajakDitanggung = $('input[name="pajak_ditanggung"]:checked').val() || 'toko';

        if (document.getElementById('pajak_persen')) {
            document.getElementById('pajak_persen').disabled = !pajakAktif;
        }
        if (document.getElementById('pajak_keterangan')) {
            document.getElementById('pajak_keterangan').disabled = !pajakAktif;
        }
        $('input[name="pajak_ditanggung"]').prop('disabled', !pajakAktif);

        if (!pajakAktif) {
            pajakPersen = 0;
            if (document.getElementById('pajak_persen')) {
                document.getElementById('pajak_persen').value = 0;
            }
        }
        var pajakNominal = Math.round((sub_total * pajakPersen) / 100);
        if (document.getElementById('pajak_nominal')) {
            document.getElementById('pajak_nominal').value = pajakNominal;
        }

        // Total akhir dipengaruhi siapa yang menanggung pajak
        var totalAkhir = (pajakAktif && pajakDitanggung === 'pembeli') ? (sub_total + pajakNominal) : sub_total;
        
        if (document.getElementById('total_akhir')) {
            document.getElementById('total_akhir').value = totalAkhir;
        }
        
        var bayar = parseInt(document.getElementById('bayar').value) || 0;
        var kembali = bayar - totalAkhir;
        
        document.getElementById('kembali').value = kembali;

        var simpanBtn = $('#btn-finish');
        var simpanPrintBtn = $('#btn-finish-print');
        if (bayar < 0) {
            simpanBtn.prop('disabled', true);
            simpanPrintBtn.prop('disabled', true);
            return;
        }
        if (totalAkhir > 0 && kembali < 0) {
            simpanBtn.prop('disabled', true);
            simpanPrintBtn.prop('disabled', true);
            return;
        }
        simpanBtn.prop('disabled', false);
        simpanPrintBtn.prop('disabled', false);
    }

    // Jalankan hitung sekali saat halaman dimuat
    $(document).ready(function() {
        hitung();

        var table = $('.js-products-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("barang.productsData") }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'kode_barcode', name: 'kode_barcode' },
                { data: 'nama_barang', name: 'nama_barang' },
                { data: 'harga_jual_formatted', name: 'harga_jual_formatted' },
                { data: 'stok', name: 'stok' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        $('.js-products-table').on('click', '.select-product-from-modal', function () {
            var barcode = $(this).data('barcode');
            // Escape meta-characters from barcode for valid jQuery selector
            var escapedBarcode = barcode.toString().replace(/([ #;&,.+*~':\"!^$()\[\]|\/])/g, "\\$1");
            var jumlah = $('#jumlah-' + escapedBarcode).val();
            var kode_penjualan = new URLSearchParams(window.location.search).get('kodepj');

            $.ajax({
                url: '{{ route("penjualan.addFromModal") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    kode_barcode: barcode,
                    jumlah: jumlah,
                    kode_penjualan: kode_penjualan
                },
                success: function(response) {
                    $('#productModal').modal('hide');
                    location.reload(); // Reload halaman untuk menampilkan item baru
                },
                error: function(response) {
                    showAlert('danger', (response.responseJSON && response.responseJSON.error) ? response.responseJSON.error : 'Terjadi kesalahan');
                }
            });
        });

        var pendingConfirmAction = null;

        function openConfirm(message, action) {
            pendingConfirmAction = action;
            $('#confirmMessage').text(message);
            $('#confirmModal').modal('show');
        }

        $('#confirmYes').on('click', function() {
            $('#confirmModal').modal('hide');
            if (pendingConfirmAction === 'save') {
                $('#action_type').val('save');
                $('#finalForm').trigger('submit');
                return;
            }
            if (pendingConfirmAction === 'print') {
                $('#action_type').val('print');
                $('#finalForm').trigger('submit');
                return;
            }
            if (pendingConfirmAction === 'cancel') {
                $('#cancelForm').trigger('submit');
            }
        });

        $('#btn-finish').on('click', function() {
            openConfirm('Transaksi tidak dapat dibatalkan setelah selesai', 'save');
        });

        $('#btn-finish-print').on('click', function() {
            openConfirm('Transaksi tidak dapat dibatalkan setelah selesai', 'print');
        });

        $('#btn-cancel').on('click', function() {
            openConfirm('Apakah Anda Yakin?', 'cancel');
        });

        $('#bayar').on('blur', function() {
            hitung();
            var total_akhir = parseInt(document.getElementById('total_akhir').value) || 0;
            var bayar = parseInt(document.getElementById('bayar').value) || 0;
            if (bayar < 0) {
                showAlert('danger', 'Bayar tidak boleh minus');
                $(this).focus();
                return;
            }
            if (total_akhir > 0 && bayar < total_akhir) {
                showAlert('danger', 'Bayar tidak boleh kurang dari Total Akhir');
                $(this).focus();
            }
        });

        $('#finalForm').on('submit', function(e) {
            hitung();
            var total_akhir = parseInt(document.getElementById('total_akhir').value) || 0;
            var bayar = parseInt(document.getElementById('bayar').value) || 0;
            if (bayar < 0) {
                e.preventDefault();
                showAlert('danger', 'Bayar tidak boleh minus');
                $('#bayar').focus();
                return;
            }
            if (total_akhir > 0 && bayar < total_akhir) {
                e.preventDefault();
                showAlert('danger', 'Bayar tidak boleh kurang dari Total Akhir');
                $('#bayar').focus();
            }
        });

        // Inline editing for quantity and discount
        $('.item-update').on('blur', function() {
            var id = $(this).data('id');
            var field = $(this).data('field');
            var value = $(this).val();
            var data = {
                _token: '{{ csrf_token() }}',
                id: id,
                field: field,
                value: value
            };

            if (field === 'diskon_item') {
                data.diskon_tipe = $(this).closest('.input-group').find('select').val();
            }

            $.ajax({
                url: '{{ route("penjualan.updateItem") }}',
                type: 'POST',
                data: data,
                success: function(response) {
                    location.reload(); // Reload untuk update total
                },
                error: function(response) {
                    showAlert('danger', (response.responseJSON && response.responseJSON.error) ? response.responseJSON.error : 'Terjadi kesalahan');
                }
            });
        });
    });
</script>
@endpush

<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Konfirmasi</h4>
            </div>
            <div class="modal-body">
                <p id="confirmMessage" style="margin: 0;"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">BATAL</button>
                <button type="button" class="btn btn-primary waves-effect" id="confirmYes">YA</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Pilih Produk --}}
<div class="modal fade" id="productModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Pilih Produk</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-striped table-hover dataTable js-products-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Barcode</th>
                            <th>Nama Barang</th>
                            <th>Harga Jual</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">TUTUP</button>
            </div>
        </div>
    </div>
</div>
