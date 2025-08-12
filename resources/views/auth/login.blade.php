@extends('layouts.app')

@section('content')
<div class="auth-container">
    <div class="auth-box">
        <a href="{{ route('index') }}">
            <img src="{{ asset('images/logo.svg') }}" alt="PT ISEKI INDONESIA" class="logo-auth">
        </a>
        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="form-row">
                <label for="username">Username</label>
                <input type="text" name="username" value="{{ old('username') }}" placeholder="Masukkan username">
            </div>

            <div class="form-row">
                <label for="password">Password</label>
                <input type="password" name="password" placeholder="Masukkan password">
            </div>

            <div class="form-action">
                <button type="submit" class="btn btn-primary">Masuk</button>
            </div>

            @if ($errors->any())
            <ul class="error-list">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            @endif
        </form>
        <br><hr><br>
        <p>Bagi member yang menggantikan proses:</p><br>
        <a href="{{ route('replacements.read') }}"">
            <button type="button" class="btn btn-primary">Pengganti</button>
        </a>
    </div>
</div>
@endsection