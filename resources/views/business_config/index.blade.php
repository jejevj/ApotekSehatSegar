@extends('layouts.app')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card" style="border-radius: 10px;">
            <div class="header">
                <h2>KONFIGURASI BISNIS</h2>
            </div>
            <div class="body">

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form action="{{ route('business-config.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Jenis Bisnis --}}
                    <div class="row">
                        <div class="col-md-6">
                            <label>Jenis Bisnis</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <select name="business_type" id="business_type" class="form-control show-tick">
                                        <option value="apotek"  {{ ($config['business_type'] ?? '') == 'apotek'  ? 'selected' : '' }}>Apotek</option>
                                        <option value="retail"  {{ ($config['business_type'] ?? '') == 'retail'  ? 'selected' : '' }}>Retail</option>
                                        <option value="fnb"     {{ ($config['business_type'] ?? '') == 'fnb'     ? 'selected' : '' }}>FnB (Food & Beverage)</option>
                                        <option value="general" {{ ($config['business_type'] ?? 'general') == 'general' ? 'selected' : '' }}>General</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6" style="padding-top: 24px;">
                            <button type="button" id="btn-load-preset" class="btn btn-info waves-effect">
                                <i class="material-icons">auto_fix_high</i> Muat Preset
                            </button>
                        </div>
                    </div>

                    <hr>
                    <h4>Label Nama</h4>

                    {{-- Label Fields --}}
                    <div class="row">
                        <div class="col-md-4">
                            <label>Label Produk</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" name="label_product" id="label_product" class="form-control"
                                        value="{{ $config['label_product'] ?? 'Produk' }}" required />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label>Label Lokasi</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" name="label_location" id="label_location" class="form-control"
                                        value="{{ $config['label_location'] ?? 'Lokasi' }}" required />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label>Label Supplier</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" name="label_supplier" id="label_supplier" class="form-control"
                                        value="{{ $config['label_supplier'] ?? 'Supplier' }}" required />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <label>Label Pelanggan</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" name="label_customer" id="label_customer" class="form-control"
                                        value="{{ $config['label_customer'] ?? 'Pelanggan' }}" required />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label>Label Pembelian</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" name="label_purchase" id="label_purchase" class="form-control"
                                        value="{{ $config['label_purchase'] ?? 'Pembelian' }}" required />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label>Label Kategori</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" name="label_category" id="label_category" class="form-control"
                                        value="{{ $config['label_category'] ?? 'Kategori' }}" required />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <label>Label Satuan</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" name="label_unit" id="label_unit" class="form-control"
                                        value="{{ $config['label_unit'] ?? 'Satuan' }}" required />
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h4>Tampilan</h4>

                    {{-- Toggles --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="checkbox" id="show_product_location" name="show_product_location"
                                    class="filled-in chk-col-blue"
                                    {{ !empty($config['show_product_location']) ? 'checked' : '' }} />
                                <label for="show_product_location">Tampilkan Lokasi Produk</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="checkbox" id="show_product_content" name="show_product_content"
                                    class="filled-in chk-col-blue"
                                    {{ !empty($config['show_product_content']) ? 'checked' : '' }} />
                                <label for="show_product_content">Tampilkan Kandungan Produk</label>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h4>Transaksi</h4>

                    <div class="row">
                        <div class="col-md-4">
                            <label>Prefix Transaksi</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" name="trx_prefix" id="trx_prefix" class="form-control"
                                        value="{{ $config['trx_prefix'] ?? 'TRX' }}" required />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label>Batas Stok Minimum</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="number" name="low_stock_threshold" id="low_stock_threshold" class="form-control"
                                        value="{{ $config['low_stock_threshold'] ?? 10 }}" min="0" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label>Footer Struk</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" name="receipt_footer" id="receipt_footer" class="form-control"
                                        value="{{ $config['receipt_footer'] ?? '' }}"
                                        placeholder="Contoh: Terima kasih telah berbelanja" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="m-t-20 text-right">
                        <button type="submit" class="btn btn-primary btn-lg waves-effect">
                            <i class="material-icons">save</i> Simpan Konfigurasi
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $('#btn-load-preset').on('click', function () {
        var businessType = $('#business_type').val();
        if (!businessType) return;

        $.ajax({
            url: '{{ route('business-config.preset', ':type') }}'.replace(':type', businessType),
            method: 'GET',
            success: function (data) {
                if (data.label_product)   $('#label_product').val(data.label_product);
                if (data.label_location)  $('#label_location').val(data.label_location);
                if (data.label_supplier)  $('#label_supplier').val(data.label_supplier);
                if (data.label_customer)  $('#label_customer').val(data.label_customer);
                if (data.label_purchase)  $('#label_purchase').val(data.label_purchase);
                if (data.label_category)  $('#label_category').val(data.label_category);
                if (data.label_unit)      $('#label_unit').val(data.label_unit);
                if (data.trx_prefix)      $('#trx_prefix').val(data.trx_prefix);

                $('#show_product_location').prop('checked', data.show_product_location === true);
                $('#show_product_content').prop('checked', data.show_product_content === true);

                // Trigger materialize label update
                $('input.form-control').each(function () {
                    if ($(this).val()) {
                        $(this).closest('.form-line').addClass('focused');
                    }
                });
            },
            error: function () {
                alert('Gagal memuat preset. Silakan coba lagi.');
            }
        });
    });
</script>
@endpush
