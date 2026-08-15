@extends('backend::staff.layouts.app')

@section('title', 'Create Ticket')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <h5 class="card-header">Create Ticket for Admin</h5>
                <div class="card-body">
                    @include('backend::partials.tickets.form')
                </div>
            </div>
        </div>
    </div>
@endsection
