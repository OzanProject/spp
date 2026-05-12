<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - {{ setting('school_name', 'Sistem SPP') }}</title>
    <style>
        :root { 
            --color-primary-custom: {{ setting('primary_color', '#435ebe') }}; 
            --bs-primary: var(--color-primary-custom);
        }
        .text-primary, .btn-primary, .bg-primary, .sidebar-item.active > .sidebar-link {
            background-color: var(--color-primary-custom) !important;
            border-color: var(--color-primary-custom) !important;
            color: #fff !important;
        }
        .text-primary { background-color: transparent !important; color: var(--color-primary-custom) !important; }
        .btn-outline-primary { color: var(--color-primary-custom) !important; border-color: var(--color-primary-custom) !important; }
        .btn-outline-primary:hover { background-color: var(--color-primary-custom) !important; color: #fff !important; }
        .sidebar-item.active > .sidebar-link { background-color: var(--color-primary-custom) !important; }
        #main { display: flex; flex-direction: column; min-height: 100vh; }
        #main-content { flex: 1; display: flex; flex-direction: column; padding: 2rem 2rem 0 2rem; }
        section.section { flex: 1; }
        footer { margin-top: auto; padding-top: 2rem; padding-bottom: 1.5rem; border-top: 1px solid rgba(0,0,0,0.05); }
    </style>
    
    <script src="https://cdn.jsdelivr.net/npm/pace-js@latest/pace.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pace-js@latest/pace-theme-default.min.css">
    <link rel="stylesheet" href="https://zuramai.github.io/mazer/demo/assets/compiled/css/app.css">
    <link rel="stylesheet" href="https://zuramai.github.io/mazer/demo/assets/compiled/css/app-dark.css">
    @if(setting('favicon'))
        <link rel="shortcut icon" href="{{ Storage::url('logos/' . setting('favicon')) }}">
    @else
        <link rel="shortcut icon" href="https://zuramai.github.io/mazer/demo/assets/static/images/logo/favicon.svg" type="image/x-icon">
        <link rel="shortcut icon" href="https://zuramai.github.io/mazer/demo/assets/static/images/logo/favicon.png" type="image/png">
    @endif
    
    <style>
        .pace {
            -webkit-pointer-events: none;
            pointer-events: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }
        .pace-inactive { display: none; }
        .pace .pace-progress {
            background: var(--color-primary-custom);
            position: fixed;
            z-index: 2000;
            top: 0;
            right: 100%;
            width: 100%;
            height: 3px;
        }
    </style>

    @yield('styles')
</head>

<body>
    <script src="https://zuramai.github.io/mazer/demo/assets/static/js/initTheme.js"></script>
    <div id="app">
        <div id="sidebar">
            @include('backend.layouts.sidebar')
        </div>
        <div id="main" class="layout-navbar navbar-fixed">
            @include('backend.layouts.navbar')

            <div id="main-content">
                @yield('content')

                @include('backend.layouts.footer')
            </div>
        </div>
    </div>
    
    <script src="https://zuramai.github.io/mazer/demo/assets/static/js/components/dark.js"></script>
    <script src="https://zuramai.github.io/mazer/demo/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="https://zuramai.github.io/mazer/demo/assets/compiled/js/app.js"></script>
    
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
