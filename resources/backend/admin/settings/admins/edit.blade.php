@extends('backend::admin.layouts.app')

@section('title', 'Edit Admin')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card lms-page-card mb-3">
                <div class="card-header lms-page-header">
                    <div class="lms-page-header__copy">
                        <p class="lms-page-header__eyebrow">Manual email triggers</p>
                        <h5 class="lms-page-header__title">Send emails to {{ $admin->name }}</h5>
                        <p class="lms-page-header__subtitle">
                            Resend verification, password reset, or welcome / password-setup emails for
                            <strong>{{ $admin->email }}</strong>.
                        </p>
                    </div>
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        @if ($admin->hasVerifiedEmail())
                            <span class="lms-badge lms-badge--success"><span class="lms-badge__dot"></span> Verified</span>
                        @else
                            <span class="lms-badge lms-badge--warning"><span class="lms-badge__dot"></span> Unverified</span>
                        @endif
                        @if ($admin->is_active)
                            <span class="lms-badge lms-badge--success"><span class="lms-badge__dot"></span> Active</span>
                        @else
                            <span class="lms-badge lms-badge--muted"><span class="lms-badge__dot"></span> Inactive</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="lms-mail-grid">
                        <div class="lms-mail-card">
                            <div class="lms-mail-card__top">
                                <div class="lms-mail-card__icon lms-mail-card__icon--verify">
                                    <i class="iconify" data-icon="bx:bx-check-shield"></i>
                                </div>
                                <div>
                                    <h6 class="lms-mail-card__title">Email verification</h6>
                                    <p class="lms-mail-card__copy">Send a fresh signed verification link to this admin.</p>
                                    <div class="lms-mail-card__meta">
                                        @if ($admin->hasVerifiedEmail())
                                            <span class="lms-badge lms-badge--success"><span class="lms-badge__dot"></span> Already verified</span>
                                        @else
                                            <span class="lms-badge lms-badge--warning"><span class="lms-badge__dot"></span> Needs verification</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="lms-mail-card__action">
                                <form action="{{ route('admin.settings.admins.send-verification-email', $admin) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="lms-filter-btn lms-filter-btn--primary" @disabled($admin->hasVerifiedEmail())>
                                        <i class="iconify" data-icon="bx:bx-send"></i>
                                        {{ $admin->hasVerifiedEmail() ? 'Already verified' : 'Send verification email' }}
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="lms-mail-card">
                            <div class="lms-mail-card__top">
                                <div class="lms-mail-card__icon lms-mail-card__icon--reset">
                                    <i class="iconify" data-icon="bx:bx-key"></i>
                                </div>
                                <div>
                                    <h6 class="lms-mail-card__title">Forgot password</h6>
                                    <p class="lms-mail-card__copy">Send a password reset link for when they cannot sign in.</p>
                                </div>
                            </div>
                            <div class="lms-mail-card__action">
                                <form action="{{ route('admin.settings.admins.send-password-reset-email', $admin) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="lms-filter-btn lms-filter-btn--ghost">
                                        <i class="iconify" data-icon="bx:bx-send"></i>
                                        Send password reset email
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="lms-mail-card">
                            <div class="lms-mail-card__top">
                                <div class="lms-mail-card__icon lms-mail-card__icon--welcome">
                                    <i class="iconify" data-icon="bx:bx-envelope-open"></i>
                                </div>
                                <div>
                                    <h6 class="lms-mail-card__title">Welcome / set password</h6>
                                    <p class="lms-mail-card__copy">Send onboarding email with a set-password button. Setting password also verifies email.</p>
                                </div>
                            </div>
                            <div class="lms-mail-card__action">
                                <form action="{{ route('admin.settings.admins.send-welcome-email', $admin) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="lms-filter-btn lms-filter-btn--primary">
                                        <i class="iconify" data-icon="bx:bx-send"></i>
                                        Send welcome email
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card lms-page-card">
                <div class="card-header lms-page-header">
                    <div class="lms-page-header__copy">
                        <p class="lms-page-header__eyebrow">Settings</p>
                        <h5 class="lms-page-header__title">Edit admin</h5>
                        <p class="lms-page-header__subtitle">Update profile details, password, and account status.</p>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.admins.update', $admin) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $admin->name) }}">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $admin->email) }}">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                                <small class="text-muted">Leave blank to keep the current password. {{ password_rule_hint() }}</small>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $admin->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary lms-btn-add">
                            <i class="iconify" data-icon="bx:bx-save"></i>
                            Update Admin
                        </button>
                        <a href="{{ route('admin.settings.admins.index') }}" class="lms-filter-btn lms-filter-btn--ghost">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
