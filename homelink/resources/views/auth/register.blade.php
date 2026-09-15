@extends('layouts.app')
@section('title','Create your account')
@section('eyebrow','Join HomeLink')
@section('content')
<div class="auth-layout register-layout">
<div class="auth-story"><img src="{{ asset('images/home.jpg') }}" alt="A welcoming home"><div class="auth-story-content"><span class="hero-kicker">GOOD PEOPLE. BETTER HOMES.</span><h1>A helping hand.<br>A fresh start.</h1><p>Find the right people for your home,<br>or bring your expertise to the neighborhood.</p></div></div>
<div class="auth-form"><p class="eyebrow">THERE’S A PLACE FOR YOU HERE</p><h1>Join HomeLink.</h1><p class="muted">A few details, and you’re right at home.</p>
<form method="POST" action="{{ route('register') }}" data-submit>@csrf
<fieldset class="field"><legend>I’m here to</legend><div class="role-picker"><label><input name="role" type="radio" value="customer" @checked(old('role',request('role','customer'))==='customer') required><span><x-icon name="house" />Find a service</span></label><label><input name="role" type="radio" value="provider" @checked(old('role',request('role'))==='provider')><span><x-icon name="briefcase-business" />Offer my services</span></label></div></fieldset>
<label class="field">Full name<input name="name" required maxlength="100" autocomplete="name" value="{{ old('name') }}" placeholder="Your full name"></label>
<label class="field">Email address<input type="email" name="email" required autocomplete="email" value="{{ old('email') }}" placeholder="you@example.com"></label>
<div class="form-row"><label class="field">Phone number<input name="phone" type="tel" required value="{{ old('phone') }}" placeholder="077 123 4567"></label><label class="field">City<input name="city" required maxlength="80" value="{{ old('city','Colombo') }}" placeholder="Colombo"></label></div>
<label class="field">Password<input type="password" name="password" required minlength="10" autocomplete="new-password"><small>At least 10 characters, with uppercase, lowercase, and a number.</small></label>
<label class="field">Confirm password<input type="password" name="password_confirmation" required autocomplete="new-password"></label>
<button class="btn btn-teal btn-full" type="submit">Create account <x-icon name="arrow-right" /></button>
</form><div class="auth-switch">Already at home here? <a href="{{ route('login') }}">Sign in</a></div>
</div></div>
@endsection
