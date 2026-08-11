@extends('backend::staff.layouts.app')

@section('title', 'My Profile')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <h5 class="card-header">Profile Details</h5>
                <div class="card-body">
                    <form action="{{ route('staff.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="d-flex align-items-start align-items-sm-center gap-4">
                            <img src="{{ $staff->avatar_url ?: avatar_placeholder($staff->initials) }}"
                                alt="Avatar"
                                class="d-block rounded"
                                height="100"
                                width="100"
                                id="uploadedAvatar"
                                style="object-fit: cover;">
                            <div class="button-wrapper">
                                <label for="upload" class="btn btn-primary me-2 mb-3" tabindex="0">
                                    <span class="d-none d-sm-block">Upload new photo</span>
                                    <i class="iconify d-block d-sm-none" data-icon="bx:bx-upload"></i>
                                    <input type="file"
                                        id="upload"
                                        name="avatar"
                                        class="account-file-input @error('avatar') is-invalid @enderror"
                                        hidden
                                        accept="image/jpeg,image/png,image/webp">
                                </label>
                                <button type="button" class="btn btn-outline-secondary account-image-reset mb-3">
                                    <i class="iconify d-block d-sm-none" data-icon="bx:bx-reset"></i>
                                    <span class="d-none d-sm-block">Reset</span>
                                </button>
                                <p class="text-muted mb-0">Allowed JPG, PNG or WEBP. Max size of 2MB</p>
                                @error('avatar')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="property-form-section">
                            <h6 class="property-form-section__title">Account</h6>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="iconify" data-icon="bx:bx-user"></i></span>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name', $staff->name) }}" required>
                                    </div>
                                    @error('name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="iconify" data-icon="bx:bx-envelope"></i></span>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            id="email" name="email" value="{{ old('email', $staff->email) }}" required>
                                    </div>
                                    @error('email')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="iconify" data-icon="bx:bx-phone"></i></span>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                            id="phone" name="phone" value="{{ old('phone', $staff->profile?->phone) }}"
                                            placeholder="Phone number">
                                    </div>
                                    @error('phone')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="job_title" class="form-label">Job Title</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="iconify" data-icon="bx:bx-briefcase"></i></span>
                                        <input type="text" class="form-control @error('job_title') is-invalid @enderror"
                                            id="job_title" name="job_title"
                                            value="{{ old('job_title', $staff->profile?->job_title) }}">
                                    </div>
                                    @error('job_title')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-0">
                                <label for="bio" class="form-label">Bio</label>
                                <textarea class="form-control @error('bio') is-invalid @enderror"
                                    id="bio" name="bio" rows="3"
                                    placeholder="A short introduction">{{ old('bio', $staff->profile?->bio) }}</textarea>
                                @error('bio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="property-form-section">
                            <h6 class="property-form-section__title">Change Password</h6>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="iconify" data-icon="bx:bx-lock-alt"></i></span>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                                            id="password" name="password" autocomplete="new-password">
                                    </div>
                                    <small class="text-muted">Leave blank to keep the current password. {{ password_rule_hint() }}</small>
                                    @error('password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="iconify" data-icon="bx:bx-lock-alt"></i></span>
                                        <input type="password" class="form-control" id="password_confirmation"
                                            name="password_confirmation" autocomplete="new-password">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">Save changes</button>
                            <a href="{{ route('staff.dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('vendor/sneat/js/pages-account-settings-account.js') }}"></script>
@endsection
