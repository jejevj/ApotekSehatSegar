@extends('layouts.app')

@section('content')
<div class="block-header">
    <h2>Laporan HPP & Margin</h2>
</div>

<div class="row clearfix">
    {{-- Summary Cards --}}
    <div class="col-md-3 col-sm-6">
        <div class="info-box bg-cyan hover-expand-effect">
            <div class="icon"><i class="material-icons">restaurant_menu</i></div>
            <div class="content">
                <div class="text">TOTAL MENU</div>
                <div class="number">{{ $data['total_menu'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="info-box bg-orange hover-expand-effect">
            <div class="icon"><i class="material-icons">money_off</i></div>
            <div class="content">
                <div class="text">RATA-RATA HPP</div>
                <div class="number">Rp {{ number_format($data['avg_hpp'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="info-box bg-green hover-expand-effect">
            <div class="icon"><i class="material-icons">trending_up</i></div>
            <div class="content">
                <div class="text">RATA-RATA MARGIN</div>
                <div class="number">{{ $data['avg_margin'] }}%</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="info-box bg-red hover-expand-effect">
            <div class="icon"><i class="material-icons">warning</i></div>
            <div class="content">
                <div class="text">BELUM ADA RESEP</div>
                <div class="number">{{ $data['menu_tanpa_resep'] }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-xs-12">
        <div class="card">
            <div class="header">
                <h2>Detail HPP per Menu</h2>
                <div class="header-dropdown">
                    <a href="{{ route('fnb.hpp.print', request()->query()) }}" target="_blank" class="btn btn-default waves-effect">
                        <i class="material-icons">print</i> Cetak
                    </a>
                </div>
            </div>
            <div class="body">
                {{-- Filter --}}
                <form method="GET" class="m-b-20">
                    <div class="row">
                        <div class="col-md-4">
                            <select name="category_id" class="form-control show-tick" onchange="this.form.submit()">
                                <option value="">-- Semua Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover dataTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Menu</th>
                                <th>Kategori</th>
                                <th>Resep</th>
                                <th class="text-right">HPP/Porsi</th>
                                <th class="text-right">Harga Jual</th>
                                <th class="text-right">Margin (Rp)</th>
                                <th class="text-right">Margin (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['rows'] as $i => $row)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $row['nama_barang'] }}</td>
                                <td>{{ $row['kategori'] }}</td>
                                <td>{{ $row['resep_nama'] ?? '<span class="text-muted">Belum ada resep</span>' }}</td>
                                <td class="text-right">
                                    @if($row['hpp'] !== null)
                                        Rp {{ number_format($row['hpp'], 0, ',', '.') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-right">Rp {{ number_format($row['harga_jual'], 0, ',', '.') }}</td>
                                <td class="text-right">
                                    @if($row['margin_nominal'] !== null)
                                        <span class="{{ $row['margin_nominal'] >= 0 ? 'text-green' : 'text-red' }}">
                                            Rp {{ number_format($row['margin_nominal'], 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if($row['margin_persen'] !== null)
                                        <span class="{{ $row['margin_persen'] >= 0 ? 'text-green' : 'text-red' }}">
                                            {{ $row['margin_persen'] }}%
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
