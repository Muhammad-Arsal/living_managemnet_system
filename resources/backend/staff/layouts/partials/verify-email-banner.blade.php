@php
    $user = Auth::guard('staff')->user();
@endphp

@if ($user && ! $user->hasVerifiedEmail())
    <div class="alert alert-warning lms-verify-banner" role="alert">
        <div>
            <strong>Verify your email.</strong>
            Your account is signed in, but <strong>{{ $user->email }}</strong> is not verified yet.
            Check your inbox for the verification link.
        </div>
        <form action="{{ route('staff.verification.send') }}" method="POST" class="mb-0 lms-verify-banner__actions">
            @csrf
            <button type="submit" class="btn btn-sm btn-warning">
                Resend verification email
            </button>
        </form>
    </div>
@endif
