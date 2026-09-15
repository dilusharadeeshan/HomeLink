@extends('layouts.app')
@section('title',$listing->exists?'Edit service':'Create service')
@section('eyebrow','Provider workspace')
@section('content')
<a class="back-link" href="{{ route('provider.services') }}"><x-icon name="arrow-left" />Back to my services</a><div class="page-heading"><div><p class="eyebrow">GREAT WORK STARTS WITH THE DETAILS</p><h1>{{ $listing->exists?'Fine-tune your service.':'Put your skills to work.' }}</h1><p>Help customers understand what you offer and what to expect.</p></div></div>
<form class="panel form-panel" method="POST" action="{{ $listing->exists?route('provider.services.update',$listing):route('provider.services.store') }}" data-submit>@csrf @if($listing->exists)@method('PUT')@endif
<div class="form-row"><label class="field">Service category<select name="category_id" required><option value="">Choose a category</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id',$listing->category_id)===$category->id || old('category_id')==$category->id)>{{ $category->name }}</option>@endforeach</select></label><label class="field">Service area / city<input name="city" required maxlength="80" value="{{ old('city',$listing->city) }}"></label></div>
<label class="field">Service title<input name="title" required maxlength="120" value="{{ old('title',$listing->title) }}" placeholder="e.g. A fresh start for your home"></label>
<label class="field">What’s included?<textarea name="description" required minlength="30" maxlength="3000" rows="6" placeholder="Describe your service, the work included, and any preparation the customer should make.">{{ old('description',$listing->description) }}</textarea><small>At least 30 characters. Be specific about the work and what it includes.</small></label>
<div class="form-row"><label class="field">Starting price (LKR)<input type="number" name="price" required min="100" max="1000000" step="1" value="{{ old('price',$listing->price) }}"></label><label class="field">Pricing note <small>Optional</small><input name="pricing_note" maxlength="160" value="{{ old('pricing_note',$listing->pricing_note) }}" placeholder="e.g. Materials charged separately"></label></div>
<label class="checkbox-field"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" @checked(old('is_active',$listing->is_active))><span>Accept new bookings<small>Your listing appears in search while this is on.</small></span></label>
<div class="form-actions"><a class="btn btn-outline" href="{{ route('provider.services') }}">Cancel</a><button type="submit" class="btn btn-teal">{{ $listing->exists?'Save changes':'Create service' }}<x-icon name="arrow-right" /></button></div>
</form>
@endsection
