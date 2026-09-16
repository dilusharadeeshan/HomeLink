<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}"><meta name="description" content="HomeLink connects your home with local electricians, plumbers, cleaners and more. Browse services and manage bookings in one place.">
<meta name="theme-color" content="#112e35"><title>@yield('title','Find your home’s next helping hand') · HomeLink</title>
<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
@vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body x-data="{ menuOpen:false, mobile:window.innerWidth <= 760 }" @resize.window="mobile=window.innerWidth <= 760" @keydown.escape.window="menuOpen=false">
<a class="skip-link" href="#main-content">Skip to content</a>
<button x-cloak x-show="menuOpen" class="sidebar-scrim" @click="menuOpen=false" aria-label="Close navigation"></button>
<aside class="sidebar" :class="{'is-open':menuOpen}" aria-label="Main navigation" :inert="mobile && !menuOpen" :aria-hidden="mobile && !menuOpen">
    <a class="brand" href="{{ route('home') }}"><span class="brand-mark"><x-icon name="house" /></span>HomeLink<span class="brand-dot">.</span></a>
    <div class="sidebar-label">YOUR HOME, SORTED</div>
    <nav class="nav-main">
        <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home','services.*') ? 'active' : '' }}"><x-icon name="layout-grid" />Explore services</a>
        @auth
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard','admin.*') ? 'active' : '' }}"><x-icon name="chart-no-axes-combined" />{{ auth()->user()->role === 'admin' ? 'Administration' : 'Overview' }}</a>
        @if(auth()->user()->role !== 'admin')<a href="{{ route('bookings.index') }}" class="nav-link {{ request()->routeIs('bookings.*') ? 'active' : '' }}"><x-icon name="calendar-days" />{{ auth()->user()->role === 'provider' ? 'Booking requests' : 'My bookings' }}</a>@endif
        @if(auth()->user()->role === 'provider')<a href="{{ route('provider.services') }}" class="nav-link {{ request()->routeIs('provider.*') ? 'active' : '' }}"><x-icon name="briefcase-business" />My services</a>@endif
        <a href="{{ route('profile') }}" class="nav-link {{ request()->routeIs('profile*') ? 'active' : '' }}"><x-icon name="user-round" />My profile</a>
        @else
        <a href="{{ route('login') }}" class="nav-link"><x-icon name="calendar-days" />My bookings</a>
        <a href="{{ route('register',['role'=>'provider']) }}" class="nav-link"><x-icon name="briefcase-business" />Become a provider</a>
        @endauth
    </nav>
    <div class="sidebar-label nav-second-label">A LITTLE GUIDANCE</div>
    <a href="{{ route('how') }}" class="nav-link {{ request()->routeIs('how') ? 'active' : '' }}"><x-icon name="circle-help" />How it works</a>
    <div class="sidebar-bottom">
        <div class="side-note"><span class="note-icon"><x-icon name="sprout" /></span><h3>A home in good hands.</h3><p>Everyday jobs. Local people.<br>A little more peace of mind.</p><a href="{{ route('how') }}">Get to know HomeLink <x-icon name="arrow-up-right" /></a></div>
        @auth<div class="account-row"><span class="avatar">{{ auth()->user()->initials() }}</span><a href="{{ route('profile') }}"><strong>{{ Str::limit(auth()->user()->name,20) }}</strong><small>{{ ucfirst(auth()->user()->role) }} account</small></a><form method="POST" action="{{ route('logout') }}">@csrf<button class="icon-button" title="Sign out" aria-label="Sign out"><x-icon name="log-out" /></button></form></div>@else
        <div class="sidebar-footer"><span class="online-dot"></span>Made for everyday living</div>
        @endauth
    </div>
</aside>
<div class="app-shell">
    <header class="topbar">
        <button class="icon-button mobile-menu" @click="menuOpen=!menuOpen" aria-label="Toggle navigation" :aria-expanded="menuOpen"><x-icon name="menu" /></button>
        <div class="breadcrumb">HomeLink <span>/</span> <strong>@yield('eyebrow','Explore services')</strong></div>
        <div class="top-actions">@if(config('homelink.demo'))<span class="demo-badge" title="Fictional accounts and reviews for the project demonstration">DEMO</span>@endif<span class="location"><x-icon name="map-pin" />Sri Lanka</span><span class="top-divider"></span>@auth<a class="avatar top-avatar" href="{{ route('profile') }}" aria-label="Open your profile">{{ auth()->user()->initials() }}</a>@else<a class="login-link" href="{{ route('login') }}">Log in</a><a class="btn btn-small btn-dark" href="{{ route('register') }}">Get started <x-icon name="arrow-up-right" /></a>@endauth</div>
    </header>
    <main id="main-content" class="main-content">
        @if(session('success'))<div class="flash flash-success" role="status"><x-icon name="circle-check" /><span>{{ session('success') }}</span></div>@endif
        @if($errors->any())<div class="flash flash-error" role="alert"><x-icon name="circle-alert" /><div><strong>Please check the following</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div></div>@endif
        @yield('content')
        <footer class="page-footer"><span>© {{ date('Y') }} HomeLink</span><span>{{ config('homelink.demo') ? 'Demo workspace · fictional data' : 'Good people. Better homes.' }}</span><a href="{{ route('how') }}">Help & booking guide <x-icon name="arrow-up-right" /></a></footer>
    </main>
</div>
<dialog id="confirmation-dialog" class="confirmation-dialog" aria-labelledby="confirm-title" aria-describedby="confirm-message">
<div class="confirm-icon"><x-icon name="circle-help" /></div><h2 id="confirm-title">Confirm your action</h2><p id="confirm-message" data-confirm-message></p><div class="form-actions"><button type="button" class="btn btn-outline" data-confirm-cancel autofocus>Go back</button><button type="button" class="btn btn-teal" data-confirm-accept>Confirm</button></div>
</dialog>
</body></html>
