@extends('layouts.app')

@section('content')
<div class="block-header">
    <h2>{{ isset($recipe) ? 'Edit Resep' : 'Tambah Resep' }}</h2>
</div>

<div class="row clearfix">
    <div class="col-md-10 col-md-offset-1">
        <div class="card">
            <div class="header"><h2>Form Resep</h2></div>
            <div class="body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="m-b-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                @if(isset($recipe))
                    <form action="{{ route('fnb.recipes.update', $recipe->id) }}" method="POST">
                    @method('PUT')
                @else
                    <form action="{{ route('fnb.recipes.store') }}" method="POST">
                @endif
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Menu <span class="text-danger">*</span></label>
                            <select name="menu_id" class="form-control show-tick" required>
                                <option value="">-- Pilih Menu --</option>
                                @foreach($menus as $menu)
                                    <option value="{{ $menu->kode_barcode }}"
                                        {{ old('menu_id', $recipe->menu_id ?? '') == $menu->kode_barcode ? 'selected' : '' }}>
                                        {{ $menu->nama_barang }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Hanya produk bertanda "Menu" yang tampil. <a href="{{ route('barang.index') }}">Kelola produk</a></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama Resep <span class="text-danger">*</span></label>
                            <div class="form-line">
                                <input type="text" name="nama_resep" class="form-control"
                                       value="{{ old('nama_resep', $recipe->nama_resep ?? '') }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Jumlah Porsi <span class="text-danger">*</span></label>
                            <div class="form-line">
                                <input type="number" name="porsi" class="form-control" min="1"
                                       value="{{ old('porsi', $recipe->porsi ?? 1) }}" required>
                            </div>
                            <small class="text-muted">HPP dihitung per porsi</small>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Keterangan</label>
                            <div class="form-line">
                                <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $recipe->keterangan ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Recipe Items --}}
                <hr>
                <h4>Komposisi Bahan Baku <span class="text-danger">*</span></h4>
                <small class="text-muted">Minimal 1 bahan baku. HPP = SUM(harga_beli × qty) / porsi</small>

                <div class="table-responsive m-t-10">
                    <table class="table table-bordered" id="recipeItemsTable">
                        <thead>
                            <tr>
                                <th>Bahan Baku</th>
                                <th width="15%">Qty</th>
                                <th width="20%">Satuan</th>
                                <th width="15%">Harga Beli</th>
                                <th width="15%">Subtotal</th>
                                <th width="8%"></th>
                            </tr>
                        </thead>
                        <tbody id="recipeItemsBody">
                            @php
                                $existingItems = old('recipe_items', isset($recipe) ? $recipe->items->map(fn($i) => [
                                    'ingredient_id' => $i->ingredient_id,
                                    'qty' => $i->qty,
                                    'unit_id' => $i->unit_id,
                                ])->toArray() : []);
                            @endphp
                            @forelse($existingItems as $idx => $item)
                                @include('fnb.recipes._item_row', ['idx' => $idx, 'item' => $item])
                            @empty
                                @include('fnb.recipes._item_row', ['idx' => 0, 'item' => []])
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-right"><strong>Estimasi HPP/Porsi:</strong></td>
                                <td><strong id="hppPreview">Rp 0</strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <button type="button" class="btn btn-default waves-effect" id="addItemBtn">
                    <i class="material-icons">add</i> Tambah Bahan
                </button>

                <div class="m-t-20">
                    <button type="submit" class="btn btn-primary waves-effect">
                        <i class="material-icons">save</i> Simpan Resep
                    </button>
                    <a href="{{ route('fnb.recipes.index') }}" class="btn btn-default waves-effect" style="margin-left:8px;">Batal</a>
                </div>

                </form>
            </div>
        </div>
    </div>
</div>

{{-- Data ingredients untuk JS --}}
@php
    $ingredientsJson = $ingredients->map(function($i) {
        return [
            'id' => $i->id,
            'nama' => $i->nama,
            'harga_beli' => $i->harga_beli,
            'unit_id' => $i->unit_id,
            'unit_nama' => optional($i->unit)->nama ?? '-',
        ];
    });
    $unitsJson = $units->map(function($u) {
        return ['id' => $u->id, 'nama' => $u->nama];
    });
@endphp
<script>
const ingredientsData = @json($ingredientsJson);
const unitsData = @json($unitsJson);
</script>
@endsection

@push('scripts')
<script>
let rowIndex = {{ count($existingItems ?? []) > 0 ? count($existingItems) : 1 }};

function buildIngredientOptions(selectedId) {
    let opts = '<option value="">-- Pilih Bahan --</option>';
    ingredientsData.forEach(i => {
        opts += `<option value="${i.id}" data-harga="${i.harga_beli}" data-unit="${i.unit_id}" ${i.id == selectedId ? 'selected' : ''}>${i.nama}</option>`;
    });
    return opts;
}

function buildUnitOptions(selectedId) {
    let opts = '';
    unitsData.forEach(u => {
        opts += `<option value="${u.id}" ${u.id == selectedId ? 'selected' : ''}>${u.nama}</option>`;
    });
    return opts;
}

function addRow(idx, item) {
    const row = `
    <tr data-idx="${idx}">
        <td>
            <select name="recipe_items[${idx}][ingredient_id]" class="form-control ingredient-select" required>
                ${buildIngredientOptions(item ? item.ingredient_id : '')}
            </select>
        </td>
        <td>
            <input type="number" name="recipe_items[${idx}][qty]" class="form-control qty-input"
                   min="0.001" step="0.001" value="${item ? item.qty : ''}" required>
        </td>
        <td>
            <select name="recipe_items[${idx}][unit_id]" class="form-control unit-select" required>
                ${buildUnitOptions(item ? item.unit_id : '')}
            </select>
        </td>
        <td class="harga-beli-cell text-right">Rp 0</td>
        <td class="subtotal-cell text-right">Rp 0</td>
        <td class="text-center">
            <button type="button" class="btn btn-danger btn-sm remove-row"><i class="material-icons">delete</i></button>
        </td>
    </tr>`;
    $('#recipeItemsBody').append(row);
    updateRowDisplay($('#recipeItemsBody tr:last'));
}

function formatRp(val) {
    return 'Rp ' + Math.round(val).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function updateRowDisplay(row) {
    const select = row.find('.ingredient-select');
    const selected = select.find('option:selected');
    const harga = parseFloat(selected.data('harga') || 0);
    const qty = parseFloat(row.find('.qty-input').val() || 0);
    row.find('.harga-beli-cell').text(formatRp(harga));
    row.find('.subtotal-cell').text(formatRp(harga * qty));
    updateHppPreview();
}

function updateHppPreview() {
    const porsi = parseInt($('input[name="porsi"]').val() || 1);
    let total = 0;
    $('#recipeItemsBody tr').each(function () {
        const select = $(this).find('.ingredient-select option:selected');
        const harga = parseFloat(select.data('harga') || 0);
        const qty = parseFloat($(this).find('.qty-input').val() || 0);
        total += harga * qty;
    });
    const hpp = porsi > 0 ? Math.floor(total / porsi) : 0;
    $('#hppPreview').text(formatRp(hpp));
}

$(document).on('change', '.ingredient-select', function () {
    const row = $(this).closest('tr');
    const selected = $(this).find('option:selected');
    const unitId = selected.data('unit');
    if (unitId) row.find('.unit-select').val(unitId);
    updateRowDisplay(row);
});

$(document).on('input', '.qty-input', function () {
    updateRowDisplay($(this).closest('tr'));
});

$('input[name="porsi"]').on('input', updateHppPreview);

$(document).on('click', '.remove-row', function () {
    if ($('#recipeItemsBody tr').length > 1) {
        $(this).closest('tr').remove();
        updateHppPreview();
    } else {
        alert('Resep harus memiliki minimal 1 bahan baku.');
    }
});

$('#addItemBtn').on('click', function () {
    addRow(rowIndex++, null);
});

// Init existing rows
$('#recipeItemsBody tr').each(function () {
    updateRowDisplay($(this));
});
</script>
@endpush
