@extends('layouts.app')

@push('head')
<script src="{{ asset('js/anime.min.js') }}"></script>
<style>
    #particle-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        overflow: hidden;
        pointer-events: none;
        z-index: 1;
    }
    .bg-particle {
        position: absolute;
        clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
        filter: blur(4px);
        pointer-events: none;
        will-change: transform;
    }
</style>
@endpush

@section('content')
<div id="particle-container"></div>
<div class="auth-container">
    <div class="auth-box">
        <a href="{{ route('index') }}" style="display:block;text-align:center;">
            <img src="{{ asset('images/logo.svg') }}" alt="PT ISEKI INDONESIA" class="logo-auth">
        </a>
        <h1 class="auth-title">Masuk</h1>
        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="form-row">
                <label for="username">Username</label>
                <div style="position:relative;">
                    <i class="material-symbols-rounded" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:1.25rem;">person</i>
                    <input type="text" name="username" value="{{ old('username') }}" placeholder="Masukkan username" style="padding-left:2.5rem;">
                </div>
            </div>

            <div class="form-row">
                <label for="password">Password</label>
                <div style="position:relative;">
                    <i class="material-symbols-rounded" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:1.25rem;">lock</i>
                    <input type="password" name="password" placeholder="Masukkan password" style="padding-left:2.5rem;">
                </div>
            </div>

            @if ($errors->any())
            <ul class="error-list">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            @endif

            <div class="form-action" style="flex-direction:column;gap:0.75rem;">
                <button type="submit" class="btn btn-primary w-full" style="padding:0.75rem;">Masuk</button>
            </div>
        </form>
        <div style="position:relative;margin:1.5rem 0;text-align:center;">
            <hr style="border:none;border-top:1px solid var(--border);">
            <span style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);background:white;padding:0 0.75rem;color:var(--text-muted);font-size:0.8125rem;">atau</span>
        </div>
        <p style="text-align:center;color:var(--text-secondary);font-size:0.875rem;margin-bottom:0.75rem;">
            Bagi member yang menggantikan proses:
        </p>
        <a href="{{ route('replacements.read') }}" class="btn btn-secondary w-full" style="padding:0.75rem;">
            <i class="material-symbols-rounded">person</i>
            Pengganti
        </a>
    </div>
</div>
@endsection

@push('scripts')
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
@endpush
