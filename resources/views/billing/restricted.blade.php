@extends('layouts.app')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card" style="border-radius: 10px; border: 1px solid #f2dede; background: #f2dede;">
            <div class="header" style="background:#e74c3c; color:#fff;">
                <h2>AKSES DIBATASI</h2>
            </div>
            <div class="body">
                <p>Anda memiliki tagihan yang perlu diselesaikan.</p>
                @if(!empty($billingInfo))
                <ul>
                    <li>Jatuh Tempo: {{ \Carbon\Carbon::parse($billingInfo['expired_at'])->timezone('Asia/Jakarta')->format('d-m-Y H:i') }}</li>
                    @if($billingInfo['jumlah_tagihan'])
                    <li>Jumlah Tagihan: Rp {{ number_format($billingInfo['jumlah_tagihan'], 0, ',', '.') }}</li>
                    @endif
                    @if($billingInfo['nama_bank'])
                    <li>Bank: {{ $billingInfo['nama_bank'] }}</li>
                    @endif
                    @if($billingInfo['no_rek'])
                    <li>No. Rekening: {{ $billingInfo['no_rek'] }}</li>
                    @endif
                </ul>
                @endif
                <p>Silakan hubungi admin untuk informasi lebih lanjut.</p>
            </div>
        </div>
    </div>
@endsection

