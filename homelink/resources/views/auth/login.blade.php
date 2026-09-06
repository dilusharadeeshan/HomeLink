@extends('layouts.app')
@section('title','Welcome back')
@section('eyebrow','Your account')
@section('content')
<div class="auth-layout">
<div class="auth-story"><img src="{{ asset('images/home.jpg') }}" alt="Comfortable home interior"><div class="auth-story-content"><span class="hero-kicker">WELCOME TO A LITTLE LESS TO DO</span><h1>Your home’s next<br>chapter starts here.</h1><p>A familiar place for all your home services.<br>Good to have you back.</p></div></div>
<div class="auth-form"><span class="auth-icon"><x-icon name="house" /></span><p class="eyebrow">MAKE YOURSELF AT HOME</p><h1>Welcome back.</h1><p class="muted">Sign in and pick up where you left off.</p>
<form method="POST" action="{{ route('login') }}" data-submit>@csrf
<label class="field">Email address<input type="email" name="email" required autocomplete="email" value="{{ old('email') }}" placeholder="you@example.com" autofocus></label>
<label class="field">Password<input type="password" name="password" required autocomplete="current-password" placeholder="Enter your password"></label>
<label class="checkbox-field"><input type="checkbox" name="remember" value="1">Keep me signed in</label>
<button class="btn btn-teal btn-full" type="submit">Sign in <x-icon name="arrow-right" /></button>
</form><div class="auth-switch">New to HomeLink? <a href="{{ route('register') }}">Create an account</a></div>
</div></div>
@endsection
