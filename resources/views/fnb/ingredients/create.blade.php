@extends('layouts.app')

@section('content')
<div class="block-header">
    <h2>Tambah Bahan Baku</h2>
</div>

<div class="row clearfix">
    <div class="col-md-8 col-md-offset-2">
        <div class="card">
            <div class="header"><h2>Form Bahan Baku</h2></div>
            <div class="body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="m-b-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                <form action="{{ route('fnb.ingredients.store') }}" method="POST">
                    @csrf
                    @include('fnb.ingredients._form')
                    <div class="m-t-20">
                        <button type="submit" class="btn btn-primary waves-effect">
                            <i class="material-icons">save</i> Simpan
                        </button>
                        <a href="{{ route('fnb.ingredients.index') }}" class="btn btn-default waves-effect" style="margin-left:8px;">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
