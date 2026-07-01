@extends('layouts.app')

@section('content')
<div class="auth-container">
    <div class="auth-box">
        <a href="{{ route('index') }}" style="display:block;text-align:center;">
            <img src="{{ asset('images/logo.svg') }}" alt="PT ISEKI INDONESIA" class="logo-auth">
        </a>
        <h1 class="auth-title">Daftar</h1>

        <form action="{{ route('register') }}" method="POST">
            @csrf

            <div class="form-row">
                <label for="name">Nama</label>
                <div style="position:relative;">
                    <i class="material-symbols-rounded" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:1.25rem;">badge</i>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Masukkan nama" style="padding-left:2.5rem;">
                </div>
            </div>

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

            <div class="form-row">
                <label for="password_confirmation">Konfirmasi Password</label>
                <div style="position:relative;">
                    <i class="material-symbols-rounded" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:1.25rem;">lock</i>
                    <input type="password" name="password_confirmation" placeholder="Masukkan kembali password" style="padding-left:2.5rem;">
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
                <button type="submit" class="btn btn-primary w-full" style="padding:0.75rem;">Daftar</button>
            </div>
        </form>
        <div style="position:relative;margin:1.5rem 0;text-align:center;">
            <hr style="border:none;border-top:1px solid var(--border);">
            <span style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);background:white;padding:0 0.75rem;color:var(--text-muted);font-size:0.8125rem;">atau</span>
        </div>
        <a href="{{ route('show.login') }}" class="btn btn-secondary w-full" style="padding:0.75rem;">
            <i class="material-symbols-rounded">login</i>
            Masuk
        </a>
    </div>
</div>
@endsection
