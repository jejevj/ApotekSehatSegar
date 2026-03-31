<tr data-idx="{{ $idx }}">
    <td>
        <select name="recipe_items[{{ $idx }}][ingredient_id]" class="form-control ingredient-select" required>
            <option value="">-- Pilih Bahan --</option>
            @foreach($ingredients as $ing)
                <option value="{{ $ing->id }}"
                    data-harga="{{ $ing->harga_beli }}"
                    data-unit="{{ $ing->unit_id }}"
                    {{ ($item['ingredient_id'] ?? '') == $ing->id ? 'selected' : '' }}>
                    {{ $ing->nama }}
                </option>
            @endforeach
        </select>
    </td>
    <td>
        <input type="number" name="recipe_items[{{ $idx }}][qty]" class="form-control qty-input"
               min="0.001" step="0.001" value="{{ $item['qty'] ?? '' }}" required>
    </td>
    <td>
        <select name="recipe_items[{{ $idx }}][unit_id]" class="form-control unit-select" required>
            @foreach($units as $unit)
                <option value="{{ $unit->id }}" {{ ($item['unit_id'] ?? '') == $unit->id ? 'selected' : '' }}>
                    {{ $unit->nama }}
                </option>
            @endforeach
        </select>
    </td>
    <td class="harga-beli-cell text-right">Rp 0</td>
    <td class="subtotal-cell text-right">Rp 0</td>
    <td class="text-center">
        <button type="button" class="btn btn-danger btn-sm remove-row">
            <i class="material-icons">delete</i>
        </button>
    </td>
</tr>
