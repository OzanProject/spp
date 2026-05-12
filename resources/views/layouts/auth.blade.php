<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - {{ setting('school_name', 'Sistem SPP') }}</title>
    @if(setting('favicon'))
        <link rel="shortcut icon" href="{{ Storage::url('logos/' . setting('favicon')) }}">
    @endif

    {{-- Bootstrap & Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --color-primary-custom: {{ setting('primary_color', '#6366f1') }};
            --font-sans: 'Inter', sans-serif;
        }

        body {
            font-family: var(--font-sans);
            background: #f0f4ff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(99, 91, 255, 0.12);
            overflow: hidden;
            max-width: 960px;
            width: 100%;
        }

        .auth-left {
            padding: 50px 48px;
        }

        .auth-right {
            background: linear-gradient(135deg, var(--color-primary-custom), #8b5cf6 50%, #06b6d4 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px 32px;
            position: relative;
            overflow: hidden;
            min-height: 500px;
        }

        .auth-right::before {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            background: rgba(255,255,255,.08);
            border-radius: 50%;
            top: -80px; right: -80px;
        }

        .auth-right::after {
            content: '';
            position: absolute;
            width: 200px; height: 200px;
            background: rgba(255,255,255,.06);
            border-radius: 50%;
            bottom: -40px; left: -40px;
        }

        .auth-right .badge-pill {
            background: rgba(255,255,255,.2);
            color: #fff;
            border-radius: 50px;
            padding: 6px 16px;
            font-size: 13px;
            backdrop-filter: blur(10px);
            margin-bottom: 24px;
        }

        .auth-right h2 { color: #fff; font-weight: 700; font-size: 28px; }
        .auth-right p  { color: rgba(255,255,255,.8); font-size: 15px; }

        .auth-right .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255,255,255,.9);
            margin-bottom: 14px;
            font-size: 14px;
        }

        .auth-right .feature-item .icon-wrap {
            width: 36px; height: 36px;
            background: rgba(255,255,255,.2);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 36px;
            text-decoration: none;
        }

        .brand-logo .logo-icon {
            width: 42px; height: 42px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            color: #fff;
            font-size: 20px;
        }

        .brand-logo span {
            font-size: 20px;
            font-weight: 700;
            color: #1e1b4b;
        }

        .auth-title {
            font-size: 28px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 6px;
        }

        .auth-subtitle {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 28px;
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        .input-group-icon {
            position: relative;
        }

        .input-group-icon .form-control {
            padding-left: 44px;
            border-radius: 10px;
            border: 1.5px solid #e5e7eb;
            height: 48px;
            font-size: 14px;
            transition: all .2s;
        }

        .input-group-icon .form-control:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,.15);
        }

        .input-group-icon .form-control.is-invalid {
            border-color: #ef4444;
        }

        .input-group-icon .icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 18px;
            z-index: 5;
        }

        .btn-login {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none;
            color: #fff;
            font-weight: 600;
            font-size: 15px;
            height: 50px;
            border-radius: 12px;
            width: 100%;
            transition: all .2s;
            letter-spacing: .3px;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(99,102,241,.35);
            color: #fff;
        }

        .form-check-input:checked {
            background-color: #6366f1;
            border-color: #6366f1;
        }

        .divider-text {
            position: relative;
            text-align: center;
            color: #9ca3af;
            font-size: 13px;
            margin: 20px 0;
        }

        .divider-text::before, .divider-text::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 42%;
            height: 1px;
            background: #e5e7eb;
        }

        .divider-text::before { left: 0; }
        .divider-text::after  { right: 0; }

        @media (max-width: 767px) {
            .auth-card { border-radius: 16px; margin: 16px; }
            .auth-left { padding: 36px 24px; }
            .auth-right { min-height: 200px; padding: 32px 24px; }
            .auth-right h2 { font-size: 20px; }
            .auth-right p { font-size: 13px; }
        }
    </style>

    @yield('styles')
</head>

<body>
    <div class="container px-3">
        <div class="auth-card mx-auto">
            <div class="row g-0">
                {{-- Konten Form --}}
                <div class="col-12 col-md-6 auth-left">
                    @yield('content')
                </div>

                {{-- Panel Kanan --}}
                <div class="col-md-6 auth-right d-none d-md-flex">
                    <div class="text-center" style="position:relative; z-index:1;">
                        <span class="badge-pill"><i class="bi bi-shield-check me-1"></i> Sistem Aman & Terpercaya</span>
                        <h2 class="mb-3">{{ setting('school_name', 'Sistem Informasi Pembayaran SPP') }}</h2>
                        <p class="mb-4">Kelola pembayaran SPP sekolah dengan mudah, cepat, dan transparan.</p>

                        <div class="text-start mt-4">
                            <div class="feature-item">
                                <div class="icon-wrap"><i class="bi bi-people-fill"></i></div>
                                <span>Manajemen data siswa lengkap</span>
                            </div>
                            <div class="feature-item">
                                <div class="icon-wrap"><i class="bi bi-receipt"></i></div>
                                <span>Generate tagihan massal otomatis</span>
                            </div>
                            <div class="feature-item">
                                <div class="icon-wrap"><i class="bi bi-bar-chart-fill"></i></div>
                                <span>Laporan keuangan real-time</span>
                            </div>
                            <div class="feature-item">
                                <div class="icon-wrap"><i class="bi bi-bell-fill"></i></div>
                                <span>Notifikasi tunggakan otomatis</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        @if(session('success'))
            Toast.fire({ icon: 'success', title: "{{ session('success') }}" });
        @endif
        @if(session('error'))
            Toast.fire({ icon: 'error', title: "{{ session('error') }}" });
        @endif
        @if(session('info'))
            Toast.fire({ icon: 'info', title: "{{ session('info') }}" });
        @endif
        @if(session('warning'))
            Toast.fire({ icon: 'warning', title: "{{ session('warning') }}" });
        @endif
    </script>
    @yield('scripts')
</body>

</html>
