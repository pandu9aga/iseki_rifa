@extends('layouts.app')

@section('content')
<div class="auth-container">
    <div class="auth-box">
        <a href="{{ route('index') }}">
            <img src="{{ asset('images/logo.svg') }}" alt="PT ISEKI INDONESIA" class="logo-auth">
        </a>

        <form action="{{ route('register') }}" method="POST">
            @csrf

            <div class="form-row">
                <label for="name">Nama</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Masukkan nama">
            </div>

            <div class="form-row">
                <label for="username">Username</label>
                <input type="username" name="username" value="{{ old('username') }}" placeholder="Masukkan username">
            </div>

            <div class="form-row">
                <label for="password">Password</label>
                <input type="password" name="password" value="{{ old('password') }}" placeholder="Masukkan password">
            </div>

            <div class="form-row">
                <label for="password_confirmation">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" placeholder="Masukkan kembali password">
            </div>

            <div class="form-action">
                <a href="{{ route('show.login') }}" class="btn btn-secondary">Masuk</a>
                <button type="submit" class="btn btn-primary">Daftar</button>
            </div>

            @if ($errors->any())
            <ul class="error-list">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            @endif
        </form>
    </div>
</div>
@endsection