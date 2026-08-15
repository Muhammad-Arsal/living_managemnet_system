@extends('backend::admin.layouts.app')

@section('title', 'Support Tickets')

@section('content')
    @include('backend::partials.tickets.index')
@endsection
