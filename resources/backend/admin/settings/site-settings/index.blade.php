@extends('backend::admin.layouts.app')

@section('title', 'Site Settings')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <h5 class="card-header">Site Settings</h5>
                <div class="card-body">
                    <form action="{{ route('admin.settings.site-settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="admin_email" class="form-label">Admin Email</label>
                                <input type="email" class="form-control @error('admin_email') is-invalid @enderror"
                                    id="admin_email" name="admin_email"
                                    value="{{ old('admin_email', $settings['admin_email'] ?? '') }}">
                                @error('admin_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="from_email" class="form-label">From Email</label>
                                <input type="email" class="form-control @error('from_email') is-invalid @enderror"
                                    id="from_email" name="from_email"
                                    value="{{ old('from_email', $settings['from_email'] ?? '') }}">
                                @error('from_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="site_name" class="form-label">Site Name</label>
                                <input type="text" class="form-control @error('site_name') is-invalid @enderror"
                                    id="site_name" name="site_name"
                                    value="{{ old('site_name', $settings['site_name'] ?? '') }}">
                                @error('site_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                    id="phone" name="phone"
                                    value="{{ old('phone', $settings['phone'] ?? '') }}">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address"
                                name="address" rows="3">{{ old('address', $settings['address'] ?? '') }}</textarea>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <hr class="my-4">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="logo" class="form-label">Logo</label>
                                <input type="file" class="form-control @error('logo') is-invalid @enderror"
                                    id="logo" name="logo" accept="image/*">
                                @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                @if (! empty($settings['logo']))
                                    <div class="mt-2">
                                        <img src="{{ Storage::disk('public_uploads')->url($settings['logo']) }}"
                                            alt="Current Logo" class="img-thumbnail" style="max-height: 100px;">
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="favicon" class="form-label">Favicon</label>
                                <input type="file" class="form-control @error('favicon') is-invalid @enderror"
                                    id="favicon" name="favicon" accept="image/*">
                                @error('favicon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                @if (! empty($settings['favicon']))
                                    <div class="mt-2">
                                        <img src="{{ Storage::disk('public_uploads')->url($settings['favicon']) }}"
                                            alt="Current Favicon" class="img-thumbnail" style="max-height: 64px;">
                                    </div>
                                @endif
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Settings</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
