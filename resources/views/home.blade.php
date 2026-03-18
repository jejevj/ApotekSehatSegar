@extends('layouts.app')

@push('styles')
<style>
    .info-box .content .number {
        font-size: 24px; /* Ukuran default yang lebih kecil dari aslinya (26px) */
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        width: 100%;
    }
    
    /* Media query untuk layar kecil agar font lebih menyesuaikan */
    @media (max-width: 1199px) {
        .info-box .content .number {
            font-size: 20px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="block-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <h2>DASHBOARD</h2>
        <div style="display: flex; gap: 10px; align-items: center;">
            <select id="globalFilter" class="form-control show-tick" style="width: 200px;">
                <option value="today">Hari Ini</option>
                <option value="7_days">7 Hari Terakhir</option>
                <option value="1_month">1 Bulan Terakhir</option>
                <option value="3_months">3 Bulan Terakhir</option>
                <option value="custom">Rentang Tanggal</option>
            </select>
        </div>
    </div>

    <div class="row clearfix m-b-20" id="customDateRange" style="display: none;">
        <div class="col-md-4 col-md-offset-8">
            <div class="input-daterange input-group" id="datepicker">
                <div class="form-line">
                    <input type="date" class="form-control" name="start" id="startDate" value="{{ date('Y-m-d') }}">
                </div>
                <span class="input-group-addon">s/d</span>
                <div class="form-line">
                    <input type="date" class="form-control" name="end" id="endDate" value="{{ date('Y-m-d') }}">
                </div>
                <span class="input-group-addon">
                    <button type="button" id="btnApplyCustom" class="btn btn-primary btn-xs">Terapkan</button>
                </span>
            </div>
        </div>
    </div>

    <!-- Widgets -->
    <div class="row clearfix">
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-pink hover-expand-effect">
                <div class="icon">
                    <i class="material-icons">playlist_add_check</i>
                </div>
                <div class="content">
                    <div class="text">BARANG TERJUAL</div>
                    <div class="number" id="valBarangTerjual" title="0">0</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-cyan hover-expand-effect">
                <div class="icon">
                    <i class="material-icons">person_add</i>
                </div>
                <div class="content">
                    <div class="text">PELANGGAN AKTIF</div>
                    <div class="number" id="valPelangganAktif" title="0">0</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-light-green hover-expand-effect">
                <div class="icon">
                    <i class="material-icons">payment</i>
                </div>
                <div class="content">
                    <div class="text">JUMLAH TRANSAKSI</div>
                    <div class="number" id="valTransaksi" title="0">0</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-orange hover-expand-effect">
                <div class="icon">
                    <i class="material-icons">attach_money</i>
                </div>
                <div class="content">
                    <div class="text">TOTAL PENDAPATAN</div>
                    <div class="number" id="valPendapatan" title="Rp 0">Rp 0</div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <div class="info-box bg-green hover-expand-effect">
                <div class="icon">
                    <i class="material-icons">account_balance_wallet</i>
                </div>
                <div class="content">
                    <div class="text">KEUNTUNGAN BERSIH</div>
                    <div class="number" id="valKeuntungan" title="Rp 0">Rp 0</div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <div class="info-box bg-blue-grey hover-expand-effect" title="Total kekayaan aset barang saat ini (Stok x Harga Beli). Tidak terpengaruh filter tanggal.">
                <div class="icon">
                    <i class="material-icons">store_mall_directory</i>
                </div>
                <div class="content">
                    <div class="text">TOTAL VALUASI ASET HARI INI</div>
                    <div class="number" title="Rp {{ number_format($totalValuasiAset, 0, ',', '.') }}">Rp {{ number_format($totalValuasiAset, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <div class="info-box bg-red hover-expand-effect" title="Total sisa hutang pembelian yang belum lunas ke distributor.">
                <div class="icon">
                    <i class="material-icons">money_off</i>
                </div>
                <div class="content">
                    <div class="text">TOTAL SISA HUTANG KE DISTRIBUTOR</div>
                    <div class="number" title="Rp {{ number_format($totalSisaHutang, 0, ',', '.') }}">Rp {{ number_format($totalSisaHutang, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        
    </div>
    <!-- #END# Widgets -->

    <!-- Chart -->
    <div class="row clearfix">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="header">
                    <h2>GRAFIK PENJUALAN</h2>
                </div>
                <div class="body">
                    <canvas id="salesChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
    <!-- #END# Chart -->

    <!-- Transaction Table -->
    <div class="row clearfix">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="header">
                    <h2>DATA TRANSAKSI</h2>
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover dataTable js-transaction-table">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Kode Penjualan</th>
                                    <th>Waktu Transaksi</th>
                                    <th>Total Pendapatan</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- #END# Transaction Table -->
</div>
@endsection

@push('scripts')
<script src="{{ asset('plugins/chartjs/Chart.bundle.js') }}"></script>
<script>
$(function () {
    let salesChart = null;

    function loadSummaryData(filter, startDate = null, endDate = null) {
        let url = '{{ route("summary.data") }}?filter=' + filter;
        if (filter === 'custom' && startDate && endDate) {
            url += '&start_date=' + startDate + '&end_date=' + endDate;
        }

        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                $('#valBarangTerjual').text(response.barang_terjual.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")).attr('title', response.barang_terjual.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
                $('#valPelangganAktif').text(response.pelanggan_aktif.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")).attr('title', response.pelanggan_aktif.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
                $('#valTransaksi').text(response.transaksi.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")).attr('title', response.transaksi.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
                $('#valPendapatan').text('Rp ' + response.pendapatan.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")).attr('title', 'Rp ' + response.pendapatan.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
                $('#valKeuntungan').text('Rp ' + response.keuntungan_bersih.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")).attr('title', 'Rp ' + response.keuntungan_bersih.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
            }
        });
    }

    function loadChartData(filter, startDate = null, endDate = null) {
        let url = '{{ route("chart.data") }}?filter=' + filter;
        if (filter === 'custom' && startDate && endDate) {
            url += '&start_date=' + startDate + '&end_date=' + endDate;
        }

        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                renderChart(response.labels, response.pendapatan, response.transaksi);
            }
        });
    }

    function renderChart(labels, dataPendapatan, dataTransaksi) {
        const ctx = document.getElementById('salesChart').getContext('2d');
        
        if (salesChart) {
            salesChart.destroy();
        }

        salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Pendapatan (Rp)',
                        data: dataPendapatan,
                        borderColor: 'rgba(255, 152, 0, 1)',
                        backgroundColor: 'rgba(255, 152, 0, 0.1)',
                        borderWidth: 2,
                        yAxisID: 'y-axis-1',
                        fill: true
                    },
                    {
                        label: 'Jumlah Transaksi',
                        data: dataTransaksi,
                        borderColor: 'rgba(139, 195, 74, 1)',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        yAxisID: 'y-axis-2',
                        type: 'line'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(tooltipItem, data) {
                            let label = data.datasets[tooltipItem.datasetIndex].label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (tooltipItem.datasetIndex === 0) {
                                label += 'Rp ' + tooltipItem.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                            } else {
                                label += tooltipItem.yLabel;
                            }
                            return label;
                        }
                    }
                },
                scales: {
                    yAxes: [
                        {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            id: 'y-axis-1',
                            ticks: {
                                beginAtZero: true,
                                callback: function(value, index, values) {
                                    if(parseInt(value) >= 1000){
                                        return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                                    } else {
                                        return 'Rp ' + value;
                                    }
                                }
                            }
                        },
                        {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            id: 'y-axis-2',
                            gridLines: {
                                drawOnChartArea: false,
                            },
                            ticks: {
                                beginAtZero: true,
                                stepSize: 1
                            }
                        }
                    ]
                }
            }
        });
    }

    // Initial load
    loadSummaryData('today');
    loadChartData('today');

    // DataTable Initialization
    let transactionTable = $('.js-transaction-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("transaction.data") }}',
            data: function (d) {
                d.filter = $('#globalFilter').val();
                if (d.filter === 'custom') {
                    d.start_date = $('#startDate').val();
                    d.end_date = $('#endDate').val();
                }
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'kode_penjualan', name: 'kode_penjualan' },
            { data: 'waktu_transaksi', name: 'waktu_transaksi' },
            { data: 'total_rp', name: 'total_akhir', className: 'text-right' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-center' }
        ]
    });

    // Filter change event
    $('#globalFilter').on('change', function() {
        const filter = $(this).val();
        if (filter === 'custom') {
            $('#customDateRange').slideDown();
        } else {
            $('#customDateRange').slideUp();
            loadSummaryData(filter);
            loadChartData(filter);
            transactionTable.ajax.reload();
        }
    });

    // Custom date apply
    $('#btnApplyCustom').on('click', function() {
        const start = $('#startDate').val();
        const end = $('#endDate').val();
        if (start && end) {
            loadSummaryData('custom', start, end);
            loadChartData('custom', start, end);
            transactionTable.ajax.reload();
        } else {
            alert('Pilih rentang tanggal terlebih dahulu');
        }
    });
});
</script>
@endpush
