<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>{{ $setting->nama_aplikasi ?? 'Apotek App' }} | Point of Sale</title>
    <!-- Favicon-->
    <link rel="icon" href="{{ $setting->favicon ? asset('images/' . $setting->favicon) : asset('favicon.ico') }}" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core Css -->
    <link href="{{ asset('plugins/bootstrap/css/bootstrap.css') }}" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="{{ asset('plugins/node-waves/waves.css') }}" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="{{ asset('plugins/animate-css/animate.css') }}" rel="stylesheet" />
    <link href="{{ asset('plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    <!-- AdminBSB Themes. You can choose a theme from css/themes instead of get all themes -->
    <link href="{{ asset('css/themes/all-themes.css') }}" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>
    <style>
        .toast-alert {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            border-radius: 5px;
        }

        .bootstrap-select.btn-group.bs-container,
        .bootstrap-select .dropdown-menu {
            z-index: 10000;
        }
    </style>
</head>

<body class="theme-pink">
    <!-- Page Loader -->
    <div class="page-loader-wrapper">
        <div class="loader">
            <div class="preloader">
                <div class="spinner-layer pl-red">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
            <p>Please wait...</p>
        </div>
    </div>
    <!-- #END# Page Loader -->
    <!-- Overlay For Sidebars -->
    <div class="overlay"></div>
    <!-- #END# Overlay For Sidebars -->

    <!-- Top Bar -->
    <nav class="navbar">
        <div class="container-fluid">
            <div class="navbar-header">
                <a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false"></a>
                <a href="javascript:void(0);" class="bars"></a>
                <a class="navbar-brand" href="{{ url('/') }}">{{ $setting->nama_aplikasi ?? 'Apotek App' }}</a>
            </div>
        </div>
    </nav>

    <section>
        <!-- Left Sidebar -->
        <aside id="leftsidebar" class="sidebar">
            <!-- User Info -->
            <div class="user-info">
                <div class="image">
                    <img src="{{ asset('images/' . (auth()->user()->foto ?? 'user.png')) }}" width="48" height="48" alt="User" />
                </div>
                <div class="info-container">
                    <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ auth()->user()->nama }}</div>
                    <div class="email">Anda login sebagai, {{ auth()->user()->level }}</div>
                    <div class="btn-group user-helper-dropdown">
                        <i class="material-icons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">keyboard_arrow_down</i>
                        <ul class="dropdown-menu pull-right">
                            <li>
                                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="material-icons">input</i>Keluar
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- #User Info -->
            <!-- Menu -->
            <div class="menu">
                <ul class="list">
                    <li class="header">Menu Utama</li>
                    @if(auth()->user()->role)
                        @php
                            $roleMenus = auth()->user()->role->menus;
                        @endphp
                        @if($roleMenus->count() === 0)
                            <li class="{{ request()->is('/') ? 'active' : '' }}">
                                <a href="{{ url('/') }}">
                                    <i class="material-icons">home</i>
                                    <span>Beranda</span>
                                </a>
                            </li>
                        @endif
                        @foreach($roleMenus as $menu)
                            @php
                                // Cek apakah user memiliki permission untuk menu ini
                                if ($menu->permission_slug && !auth()->user()->hasPermission($menu->permission_slug)) {
                                    continue;
                                }

                                $isActive = false;
                                if ($menu->route_name) {
                                    $isActive = request()->routeIs($menu->route_name . '*');
                                } elseif ($menu->url) {
                                    $isActive = request()->is(trim($menu->url, '/') . '*');
                                }
                                
                                $href = 'javascript:void(0);';
                                if ($menu->route_name) {
                                    $href = route($menu->route_name);
                                } elseif ($menu->url) {
                                    $href = url($menu->url);
                                }
                            @endphp
                            <li class="{{ $isActive ? 'active' : '' }}">
                                <a href="{{ $href }}" @if($menu->target) data-toggle="modal" data-target="{{ $menu->target }}" @endif>
                                    @if($menu->icon)
                                        <i class="material-icons">{{ $menu->icon }}</i>
                                    @endif
                                    <span>{{ $menu->name }}</span>
                                </a>
                            </li>
                        @endforeach
                    @else
                        {{-- Fallback jika role belum diset --}}
                        <li class="{{ request()->is('/') ? 'active' : '' }}">
                            <a href="{{ url('/') }}">
                                <i class="material-icons">home</i>
                                <span>Beranda</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
            <!-- #Menu -->
            <!-- Footer -->
            <div class="legal">
                <div class="copyright">
                    {!! $setting->footer_text ?? '&copy; 2026 <a href="javascript:void(0);">Apotek App</a>.' !!}
                </div>
                <div class="version">
                    <b>Version: </b> 1.1.0
                </div>
            </div>
            <!-- #Footer -->
        </aside>
    </section>

    <section class="content">
        <div class="container-fluid">
            @php
                $billingDaysLeft = !empty($billingInfo) ? (int) ($billingInfo['days_left'] ?? 999) : 999;
                $showBillingBanner = !empty($billingInfo) && $billingDaysLeft > 0 && $billingDaysLeft <= 7;
                $billingAlertClass = $billingDaysLeft <= 1 ? 'alert-danger' : ($billingDaysLeft <= 3 ? 'alert-warning' : 'alert-info');
            @endphp
            @if($showBillingBanner)
                <div class="row clearfix" style="margin-bottom: 10px;">
                    <div class="col-xs-12">
                        <div class="alert {{ $billingAlertClass }}" style="margin-bottom: 0; padding: 10px 12px;">
                            <strong>Tagihan jatuh tempo H-{{ $billingDaysLeft }}</strong>
                            <span style="margin-left: 10px;">
                                Rp {{ number_format((int) ($billingInfo['jumlah_tagihan'] ?? 0), 0, ',', '.') }}
                                {{ !empty($billingInfo['nama_bank']) ? ' - ' . $billingInfo['nama_bank'] : '' }}
                                {{ !empty($billingInfo['no_rek']) ? ' (' . $billingInfo['no_rek'] . ')' : '' }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif
            @yield('content')
        </div>
    </section>

    @include('layouts._message')

    <!-- Modal Laporan -->
    <div class="modal fade" id="smallModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Laporan Penjualan</h4>
                </div>
                <form method="POST" action="{{ route('laporan.cetak') }}" target="_blank">
                    @csrf
                    <div class="modal-body">
                        <label for="">Tanggal Awal</label>
                        <div class="form-group">
                            <div class="form-line">
                                <input type="date" name="tgl_awal" class="form-control" required />
                            </div>
                        </div>
                        <label for="">Tanggal Akhir</label>
                        <div class="form-group">
                            <div class="form-line">
                                <input type="date" name="tgl_akhir" class="form-control" required />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary waves-effect">Cetak</button>
                        <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">Tutup</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Jquery Core Js -->
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap Core Js -->
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.js') }}"></script>
    <!-- Select Plugin Js -->
    <script src="{{ asset('plugins/bootstrap-select/js/bootstrap-select.js') }}"></script>
    <!-- Slimscroll Plugin Js -->
    <script src="{{ asset('plugins/jquery-slimscroll/jquery.slimscroll.js') }}"></script>
    <!-- Waves Effect Plugin Js -->
    <script src="{{ asset('plugins/node-waves/waves.js') }}"></script>
    <!-- Jquery DataTable Plugin Js -->
    <script src="{{ asset('plugins/jquery-datatable/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js') }}"></script>

    <!-- Jquery CountTo Plugin Js -->
    <script src="{{ asset('plugins/jquery-countto/jquery.countTo.js') }}"></script>

    <!-- Custom Js -->
    <script src="{{ asset('js/admin.js') }}"></script>
    <script src="{{ asset('js/pages/tables/jquery-datatable.js') }}"></script>

    <script type="text/javascript">
    $(function () {
        $('.count-to').countTo();
    });
    </script>

    <!-- Demo Js -->
    <script src="{{ asset('js\demo.js') }}"></script>
    @stack('scripts')

    <script>
        // Automatically close the alert after 5 seconds
        window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function(){
                $(this).remove(); 
            });
        }, 5000);
    </script>
</body>

</html>
