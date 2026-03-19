@extends('layouts.app')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card" style="border-radius: 10px;">
            <div class="header">
                <h2>TAMBAH METODE PEMBAYARAN</h2>
            </div>
            <div class="body">
                <form action="{{ route('metode_pembayaran.store') }}" method="POST">
                    @csrf
                    <label for="nama">Nama Metode</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                    </div>

                    <label for="kode">Kode</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" name="kode" class="form-control" required>
                        </div>
                    </div>

                    <label for="tipe">Tipe</label>
                    <div class="form-group">
                        <div class="form-line">
                            <select name="tipe" id="tipe" class="form-control" required>
                                <option value="tunai">Tunai</option>
                                <option value="transfer">Transfer</option>
                            </select>
                        </div>
                    </div>

                    <div id="bank-fields">
                        <label for="nama_bank">Nama Bank</label>
                        <div class="form-group">
                            <div class="form-line">
                                <input type="text" name="nama_bank" class="form-control">
                            </div>
                        </div>

                        <label for="no_rekening">No. Rekening</label>
                        <div class="form-group">
                            <div class="form-line">
                                <input type="text" name="no_rekening" class="form-control">
                            </div>
                        </div>
                    </div>

                    <label>
                        <input type="checkbox" name="is_aktif" value="1" class="filled-in chk-col-pink" checked>
                        <span>Aktif</span>
                    </label>

                    <div class="m-t-20">
                        <input type="submit" value="Simpan" class="btn btn-primary waves-effect">
                        <a href="{{ route('metode_pembayaran.index') }}" class="btn btn-default waves-effect">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    function toggleBankFields() {
        var tipe = document.getElementById('tipe').value;
        var bankFields = document.getElementById('bank-fields');
        if (tipe === 'transfer') {
            bankFields.style.display = 'block';
        } else {
            bankFields.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('tipe').addEventListener('change', toggleBankFields);
        toggleBankFields();
    });
</script>
@endpush

