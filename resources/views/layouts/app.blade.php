<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Iseki - RIFA')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <link rel="stylesheet" href="{{asset('css/icon.css')}}" />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    <link rel="stylesheet" href="{{asset('css/select2.min.css')}}" />
    <link rel="stylesheet" href="{{ asset('assets/datatables/dataTables.dataTables.css') }}" />
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/datatables/dataTables.js') }}"></script>
    <script src="{{ asset('js/utils.js') }}?v={{ filemtime(public_path('js/utils.js')) }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>

    <!-- Dynamic Favicon -->
    <script src="/iseki_pro_app/js/dynamic-favicon.js"></script>
    <script>document.addEventListener("DOMContentLoaded", function() { setDynamicFavicon("calendar_month", "Rifa"); });</script>

    @stack('head')
</head>

<body>

    @if (!Route::is('show.register'))
    @include('partials.navbar')
    @endif

    <div class="content">
        @yield('content')
    </div>

    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });

        // Setup CSRF header otomatis untuk jQuery AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Global Interceptor untuk fetch API: menangani error 419 (Page Expired / Session Expired)
        const originalFetch = window.fetch;
        window.fetch = async function(...args) {
            try {
                const response = await originalFetch.apply(this, args);
                if (response.status === 419) {
                    alert('Sesi Anda telah berakhir atau halaman kedaluwarsa. Halaman akan dimuat ulang secara otomatis.');
                    window.location.reload();
                    return response;
                }
                return response;
            } catch (err) {
                throw err;
            }
        };

        // Tangani juga error 419 pada jQuery AJAX
        $(document).ajaxError(function(event, jqXHR) {
            if (jqXHR.status === 419) {
                alert('Sesi Anda telah berakhir atau halaman kedaluwarsa. Halaman akan dimuat ulang secara otomatis.');
                window.location.reload();
            }
        });

        // Periodic keep-alive ping setiap 15 menit agar session tidak hangus saat tab dibiarkan terbuka
        setInterval(function() {
            originalFetch('{{ url("/csrf-token") }}', { method: 'GET' })
                .then(r => r.json())
                .then(data => {
                    if (data && data.token) {
                        const meta = document.querySelector('meta[name="csrf-token"]');
                        if (meta) meta.setAttribute('content', data.token);
                        $.ajaxSetup({
                            headers: { 'X-CSRF-TOKEN': data.token }
                        });
                    }
                })
                .catch(() => {});
        }, 15 * 60 * 1000);
    </script>

    @stack('scripts')
</body>

</html>
