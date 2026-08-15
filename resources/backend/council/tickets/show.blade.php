@extends('backend::council.layouts.app')

@section('title', $ticket->reference)

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card lms-page-card">
                <div class="card-header lms-page-header">
                    <div class="lms-page-header__copy">
                        <p class="lms-page-header__eyebrow">Support Tickets</p>
                        <h5 class="lms-page-header__title">{{ $ticket->subject }}</h5>
                    </div>
                </div>
                <div class="card-body">
                    @include('backend::partials.tickets.show')
                </div>
            </div>
        </div>
    </div>
@endsection
