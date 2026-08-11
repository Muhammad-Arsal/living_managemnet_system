@extends('backend::admin.layouts.auth')

@section('title', 'Forgot Password')
@section('page-title', 'Forgot Password')
@section('page-subtitle', 'Enter your email to receive a reset link')

@section('content')
    <form method="POST" action="{{ route('admin.password.email') }}">
        @csrf

        <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}"
                placeholder="admin@example.com" autofocus>
            @if ($errors->has('email'))
                <span class="error-text">{{ $errors->first('email') }}</span>
            @endif
        </div>

        <button type="submit" class="btn-login">Send Reset Link</button>

        <div class="auth-link-container">
            <a href="{{ route('admin.login') }}" class="auth-link">Back to Login</a>
        </div>
    </form>
@endsection
