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

    <div class="row clearfix">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="header">
                    <h2><a href="#transactionCardBody" data-toggle="collapse">DATA TRANSAKSI TERAKHIR</a></h2>
                    <ul class="header-dropdown m-r--5">
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">more_vert</i>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li><a href="#transactionCardBody" data-toggle="collapse">Toggle</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="body collapse in" id="transactionCardBody">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover dataTable js-transaction-table">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Kode Penjualan</th>
                                    <th>Waktu Transaksi</th>
                                    <th>Total</th>
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

    <!-- Charts -->
    <div class="row clearfix">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="header">
                    <h2><a href="#salesChartCardBody" data-toggle="collapse">GRAFIK PENJUALAN</a></h2>
                    <ul class="header-dropdown m-r--5">
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">more_vert</i>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li><a href="#salesChartCardBody" data-toggle="collapse">Toggle</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="body collapse in" id="salesChartCardBody">
                    <canvas id="salesChart" height="150"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="card">
                <div class="header">
                    <h2><a href="#categorySalesChartCardBody" data-toggle="collapse">PENJUALAN PER KATEGORI</a></h2>
                    <ul class="header-dropdown m-r--5">
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">more_vert</i>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li><a href="#categorySalesChartCardBody" data-toggle="collapse">Toggle</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="body collapse in" id="categorySalesChartCardBody">
                    <canvas id="categorySalesChart" height="150"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="card">
                <div class="header">
                    <h2><a href="#topProductsCardBody" data-toggle="collapse">TOP 10 PRODUK TERLARIS</a></h2>
                    <ul class="header-dropdown m-r--5">
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">more_vert</i>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li><a href="#topProductsCardBody" data-toggle="collapse">Toggle</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="body collapse in" id="topProductsCardBody">
                    <div class="table-responsive">
                        <table class="table table-hover dashboard-task-infos">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Produk</th>
                                    <th>Jumlah Terjual</th>
                                </tr>
                            </thead>
                            <tbody id="topProductsTableBody">
                                <!-- Data will be loaded here by AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- #END# Charts -->
</div>
@endsection

@push('scripts')
<script src="{{ asset('plugins/chartjs/Chart.bundle.js') }}"></script>
<script>
$(function () {
    let salesChart, categorySalesChart;

    function formatCurrency(value) {
        return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

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
                $('#valPendapatan').text(formatCurrency(response.pendapatan)).attr('title', formatCurrency(response.pendapatan));
                $('#valKeuntungan').text(formatCurrency(response.keuntungan_bersih)).attr('title', formatCurrency(response.keuntungan_bersih));
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
                renderSalesChart(response.labels, response.pendapatan, response.transaksi);
            }
        });
    }

    function loadCategoryChartData(filter, startDate = null, endDate = null) {
        let url = '{{ route("category.chart.data") }}?filter=' + filter;
        if (filter === 'custom' && startDate && endDate) {
            url += '&start_date=' + startDate + '&end_date=' + endDate;
        }

        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                renderCategoryChart(response);
            }
        });
    }

    function loadTopProductsData(filter, startDate = null, endDate = null) {
        let url = '{{ route("top.products.data") }}?filter=' + filter;
        if (filter === 'custom' && startDate && endDate) {
            url += '&start_date=' + startDate + '&end_date=' + endDate;
        }

        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                renderTopProductsTable(response);
            }
        });
    }

    function renderSalesChart(labels, dataPendapatan, dataTransaksi) {
        const ctx = document.getElementById('salesChart').getContext('2d');
        if (salesChart) salesChart.destroy();
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
                            if (label) label += ': ';
                            if (tooltipItem.datasetIndex === 0) {
                                label += formatCurrency(tooltipItem.yLabel);
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
                            type: 'linear', display: true, position: 'left', id: 'y-axis-1',
                            ticks: { beginAtZero: true, callback: function(value) { return formatCurrency(value); } }
                        },
                        {
                            type: 'linear', display: true, position: 'right', id: 'y-axis-2',
                            gridLines: { drawOnChartArea: false },
                            ticks: { beginAtZero: true, stepSize: 1 }
                        }
                    ]
                }
            }
        });
    }

    function renderCategoryChart(data) {
        const ctx = document.getElementById('categorySalesChart').getContext('2d');
        const labels = data.map(item => item.nama_kategori);
        const values = data.map(item => item.total_terjual);

        if (categorySalesChart) categorySalesChart.destroy();
        categorySalesChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40',
                        '#E7E9ED', '#7AC142', '#F44336', '#2196F3'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: { position: 'bottom' },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            let label = data.labels[tooltipItem.index] || '';
                            let value = data.datasets[0].data[tooltipItem.index];
                            let total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                            let percentage = total > 0 ? ((value / total) * 100).toFixed(2) : 0;
                            return ` ${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        });
    }

    function renderTopProductsTable(data) {
        const tableBody = $('#topProductsTableBody');
        tableBody.empty();
        if (data.length > 0) {
            data.forEach((item, index) => {
                tableBody.append(`
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.nama_barang}</td>
                        <td><span class="badge bg-green">${item.total_terjual}</span></td>
                    </tr>
                `);
            });
        } else {
            tableBody.append('<tr><td colspan="3" class="text-center">Tidak ada data</td></tr>');
        }
    }

    function loadAllData(filter, startDate = null, endDate = null) {
        loadSummaryData(filter, startDate, endDate);
        loadChartData(filter, startDate, endDate);
        loadCategoryChartData(filter, startDate, endDate);
        loadTopProductsData(filter, startDate, endDate);
        if ($.fn.DataTable.isDataTable('.js-transaction-table')) {
            $('.js-transaction-table').DataTable().ajax.reload();
        }
    }

    // Initial load
    loadAllData('today');

    // DataTable Initialization
    $('.js-transaction-table').DataTable({
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
            loadAllData(filter);
        }
    });

    // Custom date apply
    $('#btnApplyCustom').on('click', function() {
        const start = $('#startDate').val();
        const end = $('#endDate').val();
        if (start && end) {
            loadAllData('custom', start, end);
        } else {
            alert('Pilih rentang tanggal terlebih dahulu');
        }
    });
});
</script>
@endpush
