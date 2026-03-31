<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>Setup Bisnis | POS</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">
    <link href="{{ asset('plugins/bootstrap/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/node-waves/waves.css') }}" rel="stylesheet" />
    <link href="{{ asset('plugins/animate-css/animate.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        body { background: #f5f5f5; }
        .setup-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
        }
        .setup-card {
            width: 100%;
            max-width: 520px;
        }
        .setup-card .card {
            border-radius: 10px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }
        .setup-header {
            text-align: center;
            margin-bottom: 24px;
        }
        .setup-header h3 {
            font-weight: 700;
            color: #333;
            margin-bottom: 4px;
        }
        .setup-header p {
            color: #888;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="setup-wrapper">
        <div class="setup-card">
            <div class="setup-header">
                <h3>Setup Bisnis</h3>
                <p>Lengkapi informasi bisnis Anda untuk memulai</p>
            </div>
            <div class="card">
                <div class="body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="m-b-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('setup.store') }}">
                        @csrf

                        <div class="form-group">
                            <label>Nama Bisnis <span class="text-danger">*</span></label>
                            <div class="form-line {{ $errors->has('business_name') ? 'error' : '' }}">
                                <input type="text"
                                       name="business_name"
                                       class="form-control"
                                       placeholder="Contoh: Apotek Sehat Segar"
                                       value="{{ old('business_name') }}"
                                       required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Jenis Bisnis <span class="text-danger">*</span></label>
                            <div class="form-line {{ $errors->has('business_type') ? 'error' : '' }}">
                                <select name="business_type" class="form-control show-tick" required>
                                    <option value="">-- Pilih Jenis Bisnis --</option>
                                    <option value="apotek" {{ old('business_type') === 'apotek' ? 'selected' : '' }}>Apotek</option>
                                    <option value="retail" {{ old('business_type') === 'retail' ? 'selected' : '' }}>Retail / Toko</option>
                                    <option value="fnb" {{ old('business_type') === 'fnb' ? 'selected' : '' }}>Food & Beverage (FnB)</option>
                                    <option value="general" {{ old('business_type') === 'general' ? 'selected' : '' }}>Umum (General)</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Alamat</label>
                            <div class="form-line">
                                <textarea name="address"
                                          class="form-control"
                                          rows="3"
                                          placeholder="Alamat lengkap bisnis (opsional)">{{ old('address') }}</textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Telepon</label>
                            <div class="form-line">
                                <input type="text"
                                       name="phone"
                                       class="form-control"
                                       placeholder="Contoh: 08123456789 (opsional)"
                                       value="{{ old('phone') }}"
                                       maxlength="20">
                            </div>
                        </div>

                        <div class="m-t-20">
                            <button type="submit" class="btn btn-block bg-pink waves-effect" style="border-radius:4px;">
                                <i class="material-icons" style="vertical-align:middle;font-size:18px;">check_circle</i>
                                Simpan & Mulai
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.js') }}"></script>
    <script src="{{ asset('plugins/node-waves/waves.js') }}"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
</body>
</html>
