@extends('backend::staff.layouts.auth')

@section('title', ($isSetup ?? false) ? 'Set Password' : 'Reset Password')
@section('page-title', ($isSetup ?? false) ? 'Set Password' : 'Reset Password')
@section('page-subtitle', ($isSetup ?? false) ? 'Choose a password for your account' : 'Enter your new password')

@section('content')
    @php $resetEmail = old('email', $email ?? ''); @endphp
    <form method="POST" action="{{ route('staff.password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" value="{{ $resetEmail }}" @if($resetEmail !== '') readonly style="background:#f5f5f5;cursor:not-allowed;" @endif>
            @if ($errors->has('email'))
                <span class="error-text">{{ $errors->first('email') }}</span>
            @endif
        </div>
        <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
            <label for="password">{{ ($isSetup ?? false) ? 'Password' : 'New Password' }}</label>
            <input type="password" id="password" name="password" placeholder="Enter password">
            <small class="text-muted" style="display:block;margin-top:0.35rem;">{{ password_rule_hint() }}</small>
            @if ($errors->has('password'))
                <span class="error-text">{{ $errors->first('password') }}</span>
            @endif
        </div>
        <div class="form-group {{ $errors->has('password_confirmation') ? 'has-error' : '' }}">
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm password">
            @if ($errors->has('password_confirmation'))
                <span class="error-text">{{ $errors->first('password_confirmation') }}</span>
            @endif
        </div>
        <button type="submit" class="btn-login">{{ ($isSetup ?? false) ? 'Set Password' : 'Reset Password' }}</button>
        <div class="auth-link-container">
            <a href="{{ route('staff.login') }}" class="auth-link">Back to Login</a>
        </div>
    </form>
@endsection
