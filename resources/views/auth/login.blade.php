@extends('layouts.auth')

@section('title', 'Login')
@section('page-title', 'Login')
@section('page-subtitle', 'Sign in to access the dashboard')

@section('content')
    <form method="POST" action="{{ route('login.post') }}">
        @csrf

        <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email"
                value="{{ old('email') }}"
                placeholder="admin@example.com" autofocus>
            @if ($errors->has('email'))
                <span class="error-text">{{ $errors->first('email') }}</span>
            @endif
        </div>

        <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password">
            @if ($errors->has('password'))
                <span class="error-text">{{ $errors->first('password') }}</span>
            @endif
        </div>

        <div class="remember-me">
            <input type="checkbox" id="remember" name="remember" value="1">
            <label for="remember">Keep me signed in</label>
        </div>

        <button type="submit" class="btn-login">Sign In</button>
    </form>
@endsection
