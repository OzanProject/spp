<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - {{ setting('school_name', 'Portal Siswa') }}</title>
    
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
        #main-content { flex: 1; display: flex; flex-direction: column; }
        .page-content { flex: 1; min-height: 70vh; }
        footer { margin-top: auto; padding-top: 2rem; padding-bottom: 1.5rem; border-top: 1px solid rgba(0,0,0,0.05); }
    </style>
    
    <link rel="stylesheet" href="https://zuramai.github.io/mazer/demo/assets/compiled/css/app.css">
    <link rel="stylesheet" href="https://zuramai.github.io/mazer/demo/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="https://zuramai.github.io/mazer/demo/assets/compiled/css/iconly.css">
    
    @if(setting('favicon'))
        <link rel="shortcut icon" href="{{ Storage::url('logos/' . setting('favicon')) }}">
    @else
        <link rel="shortcut icon" href="https://zuramai.github.io/mazer/demo/assets/static/images/logo/favicon.svg" type="image/x-icon">
    @endif
    
    @yield('styles')
</head>

<body>
    <script src="https://zuramai.github.io/mazer/demo/assets/static/js/initTheme.js"></script>
    <div id="app">
        <div id="sidebar">
            @include('siswa.layouts.sidebar')
        </div>
        <div id="main" class="layout-navbar navbar-fixed">
            
            @include('siswa.layouts.navbar')

            <div id="main-content" class="pb-0">
                <div class="page-content px-4">
                    @yield('content')
                </div>

                @include('siswa.layouts.footer')
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
