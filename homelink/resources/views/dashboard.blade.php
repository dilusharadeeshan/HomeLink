@extends('layouts.app')
@section('title','Your overview')
@section('eyebrow','Overview')
@section('content')
@php($provider = auth()->user()->role==='provider')
<div class="page-heading"><div><p class="eyebrow">A LITTLE CLARITY FOR YOUR DAY</p><h1>Hello, {{ Str::before(auth()->user()->name,' ') }}<span class="teal">.</span></h1><p>{{ $provider ? 'Your services, your schedule, all in one place.' : 'Your home’s to-do list is in good hands.' }}</p></div><a class="btn btn-teal" href="{{ $provider ? route('provider.services.create') : route('home') }}">{{ $provider ? 'Add a service' : 'Find a service' }}<x-icon name="plus" /></a></div>
<div class="stats-grid" data-stagger>
@foreach([['calendar-days','Total bookings',$counts['total'],'Every helping hand'],['clock-3','Active bookings',$counts['upcoming'],'Pending, accepted or in progress'],['circle-check','Completed',$counts['completed'],'Jobs marked as complete'],['wallet', $provider?'Completed job value':'Completed service value','LKR '.number_format($counts['value']),'Quoted value · payments handled directly']] as [$icon,$label,$value,$note])
<div class="stat-card"><div class="stat-label">{{ $label }}<x-icon :name="$icon" /></div><strong>{{ $value }}</strong><small>{{ $note }}</small></div>@endforeach</div>
<section class="dashboard-banner"><div><span class="eyebrow">ONE LESS THING ON YOUR LIST</span><h2>{{ $provider ? 'Good work starts with a clear plan.' : 'Your next home refresh is a click away.' }}</h2><p>{{ $provider ? 'Review new requests and keep customers up to date as each job progresses.' : 'Browse local expertise for the little fixes and bigger plans.' }}</p></div><x-icon :name="$provider ? 'clipboard-check' : 'armchair'" /></section>
<section class="panel"><div class="section-heading"><h2>Recent bookings</h2><a class="text-link" href="{{ route('bookings.index') }}">View all <x-icon name="arrow-up-right" /></a></div>
@include('partials.booking-list',['bookings'=>$bookings])
</section>
@endsection
