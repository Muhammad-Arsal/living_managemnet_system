@extends('backend::admin.layouts.app')

@section('title', 'Edit Admin')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-3">
                <h5 class="card-header">Send emails to admin</h5>
                <div class="card-body">
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 border rounded p-3">
                            <div>
                                <strong>Email verification</strong>
                                <p class="text-muted small mb-0">Send the email verification link.</p>
                            </div>
                            <form action="{{ route('admin.settings.admins.send-verification-email', $admin) }}" method="POST">@csrf
                                <button type="submit" class="btn btn-outline-primary">Send verification email</button>
                            </form>
                        </div>
                        <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 border rounded p-3">
                            <div>
                                <strong>Password reset</strong>
                                <p class="text-muted small mb-0">Send a forgot-password reset link.</p>
                            </div>
                            <form action="{{ route('admin.settings.admins.send-password-reset-email', $admin) }}" method="POST">@csrf
                                <button type="submit" class="btn btn-outline-primary">Send password reset email</button>
                            </form>
                        </div>
                        <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 border rounded p-3">
                            <div>
                                <strong>Welcome / password setup</strong>
                                <p class="text-muted small mb-0">Send the onboarding email with a password setup link.</p>
                            </div>
                            <form action="{{ route('admin.settings.admins.send-welcome-email', $admin) }}" method="POST">@csrf
                                <button type="submit" class="btn btn-outline-primary">Send welcome email</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <h5 class="card-header">Edit Admin</h5>
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
                        <button type="submit" class="btn btn-primary">Update Admin</button>
                        <a href="{{ route('admin.settings.admins.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
