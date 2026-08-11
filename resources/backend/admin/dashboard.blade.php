@extends('backend::admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold mb-1">Welcome, {{ Auth::guard('admin')->user()->name }}</h4>
            <p class="text-muted mb-0">Admin panel overview</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="iconify iconify-lg" data-icon="bx:bx-home-circle"></i>
                            </span>
                        </div>
                        <div>
                            <h5 class="mb-1">Admin Dashboard</h5>
                            <p class="text-muted mb-0">
                                Manage staff, council, settings, and email templates from this panel.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
