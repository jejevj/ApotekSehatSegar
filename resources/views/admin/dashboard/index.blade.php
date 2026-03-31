@extends('admin.layouts.app')

@section('title', 'Dashboard Platform')

@section('content')
<div class="block-header">
    <h2>Dashboard Platform</h2>
</div>

<div class="row clearfix">
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="info-box bg-blue hover-expand-effect">
            <div class="icon"><i class="material-icons">store</i></div>
            <div class="content">
                <div class="text">Total Toko</div>
                <div class="number">{{ $totalStores }}</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="info-box bg-green hover-expand-effect">
            <div class="icon"><i class="material-icons">check_circle</i></div>
            <div class="content">
                <div class="text">Toko Aktif</div>
                <div class="number">{{ $activeStores }}</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="info-box bg-orange hover-expand-effect">
            <div class="icon"><i class="material-icons">people</i></div>
            <div class="content">
                <div class="text">Total Pengguna</div>
                <div class="number">{{ $totalUsers }}</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
        <div class="info-box bg-pink hover-expand-effect">
            <div class="icon"><i class="material-icons">receipt</i></div>
            <div class="content">
                <div class="text">Transaksi Hari Ini</div>
                <div class="number">{{ $todayTransactions }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-xs-12">
        <div class="card">
            <div class="header">
                <h2>Aksi Cepat</h2>
            </div>
            <div class="body">
                <a href="{{ route('admin.stores.index') }}" class="btn btn-primary waves-effect">
                    <i class="material-icons">store</i> Kelola Toko
                </a>
                <a href="{{ route('admin.stores.create') }}" class="btn btn-success waves-effect" style="margin-left:8px;">
                    <i class="material-icons">add</i> Buat Toko Baru
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
