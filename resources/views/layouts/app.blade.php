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
    <script src="{{ asset('js/anime.min.js') }}"></script>
    <script src="{{ asset('assets/datatables/dataTables.js') }}"></script>
    <script src="{{ asset('js/utils.js') }}?v={{ filemtime(public_path('js/utils.js')) }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>

    <!-- Dynamic Favicon -->
    <script src="/iseki_pro_app/js/dynamic-favicon.js"></script>
    <script>document.addEventListener("DOMContentLoaded", function() { setDynamicFavicon("calendar_month", "Rifa"); });</script>

    <style>
        #particle-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            pointer-events: none;
            z-index: 3;
        }
        .bg-particle {
            position: absolute;
            clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
            filter: blur(4px);
            pointer-events: none;
            will-change: transform;
        }
    </style>
</head>

<body>
    <div id="particle-container"></div>

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
    </script>

    <script>
        (function() {
            var particleCount = 20;
            var colors = [
                '236, 5, 125',
                '200, 50, 150',
                '161, 94, 207',
                '92, 124, 250',
                '0, 180, 200',
                '180, 100, 200',
                '255, 100, 150',
            ];
            function rand(min, max) { return Math.random() * (max - min) + min; }
            function randInt(min, max) { return Math.floor(rand(min, max + 1)); }
            function createParticle() {
                var el = document.createElement('div');
                el.className = 'bg-particle';
                var size = rand(30, 150);
                var color = colors[randInt(0, colors.length - 1)];
                var opacity = rand(0.03, 0.09);
                el.style.width = size + 'px';
                el.style.height = size + 'px';
                
                var winW = window.innerWidth;
                var winH = window.innerHeight;
                var leftPos = rand(-10, 110) / 100 * winW;
                var topPos = rand(0, 120) / 100 * winH;
                
                el.style.left = leftPos + 'px';
                el.style.top = topPos + 'px';
                el.style.background = 'rgba(' + color + ', ' + opacity + ')';
                
                var container = document.getElementById('particle-container');
                if (container) {
                    container.appendChild(el);
                } else {
                    document.body.appendChild(el);
                }
                
                var distance = topPos + winH + 200;
                anime({
                    targets: el,
                    translateY: -distance,
                    rotate: rand(-30, 30),
                    duration: randInt(12000, 28000),
                    easing: 'linear',
                    complete: function() {
                        el.remove();
                        createParticle();
                    }
                });
            }
            for (var i = 0; i < particleCount; i++) {
                setTimeout(createParticle, randInt(0, 6000));
            }
        })();
    </script>
</body>

</html>
