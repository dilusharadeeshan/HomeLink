@extends('layouts.app')
@section('title','Your bookings')
@section('eyebrow','Bookings')
@section('content')
<div class="page-heading"><div><p class="eyebrow">EVERY DETAIL, IN ONE PLACE</p><h1>{{ auth()->user()->role==='provider' ? 'Your booking requests.' : 'Your home, in progress.' }}</h1><p>Follow every helping hand from the first request to the final detail.</p></div></div>
<nav class="filter-tabs" aria-label="Filter bookings"><a class="{{ !request('status')?'selected':'' }}" href="{{ route('bookings.index') }}">All bookings</a>@foreach(\App\Enums\BookingStatus::cases() as $status)<a class="{{ request('status')===$status->value?'selected':'' }}" href="{{ route('bookings.index',['status'=>$status->value]) }}">{{ $status->label() }}</a>@endforeach</nav>
<section class="panel">@include('partials.booking-list')<div class="pagination">{{ $bookings->links() }}</div></section>
@endsection
