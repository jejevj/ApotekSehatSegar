<div class="form-group">
    <label>Nama Bahan Baku <span class="text-danger">*</span></label>
    <div class="form-line">
        <input type="text" name="nama" class="form-control" value="{{ old('nama', $ingredient->nama ?? '') }}" required>
    </div>
</div>

<div class="form-group">
    <label>Satuan <span class="text-danger">*</span></label>
    <select name="unit_id" class="form-control show-tick" required>
        <option value="">-- Pilih Satuan --</option>
        @foreach($units as $unit)
            <option value="{{ $unit->id }}" {{ old('unit_id', $ingredient->unit_id ?? '') == $unit->id ? 'selected' : '' }}>
                {{ $unit->nama }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label>Harga Beli (Rp) <span class="text-danger">*</span></label>
    <div class="form-line">
        <input type="number" name="harga_beli" class="form-control" min="0"
               value="{{ old('harga_beli', $ingredient->harga_beli ?? 0) }}" required>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Stok Saat Ini <span class="text-danger">*</span></label>
            <div class="form-line">
                <input type="number" name="stok" class="form-control" min="0" step="0.001"
                       value="{{ old('stok', $ingredient->stok ?? 0) }}" required>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Stok Minimum (Peringatan)</label>
            <div class="form-line">
                <input type="number" name="stok_minimum" class="form-control" min="0" step="0.001"
                       value="{{ old('stok_minimum', $ingredient->stok_minimum ?? 0) }}">
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <label>Keterangan</label>
    <div class="form-line">
        <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan', $ingredient->keterangan ?? '') }}</textarea>
    </div>
</div>
