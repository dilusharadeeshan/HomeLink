@extends('layouts.app')
@section('title', 'Edit service listing')
@section('eyebrow', 'Administration')
@section('content')
<div class="page-heading"><div><p class="eyebrow">CATALOG MODERATION</p><h1>Edit service</h1><p>Update this listing on behalf of {{ $listing->provider->name }}.</p></div><a class="btn btn-outline" href="{{ route('admin.services') }}">Back to services</a></div>
@include('admin.partials.nav')
<section class="panel form-panel">
    <form method="POST" action="{{ route('admin.services.update', $listing) }}">@csrf @method('PUT')
        <div class="form-row">
            <label class="field">Service title<input name="title" maxlength="120" required value="{{ old('title', $listing->title) }}"></label>
            <label class="field">Category<select name="category_id" required>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((string) old('category_id', $listing->category_id) === (string) $category->id)>{{ $category->name }}</option>@endforeach</select></label>
        </div>
        <label class="field">Description<textarea name="description" rows="6" minlength="30" maxlength="3000" required>{{ old('description', $listing->description) }}</textarea></label>
        <div class="form-row">
            <label class="field">Price in LKR<input name="price" type="number" min="100" max="1000000" step="1" required value="{{ old('price', $listing->price) }}"></label>
            <label class="field">Pricing note <small>Optional</small><input name="pricing_note" maxlength="160" value="{{ old('pricing_note', $listing->pricing_note) }}"></label>
            <label class="field">Service city<input name="city" maxlength="80" required value="{{ old('city', $listing->city) }}"></label>
        </div>
        <input type="hidden" name="is_active" value="0"><label class="checkbox-field"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $listing->is_active))><span>Show this service in the catalog</span></label>
        <div class="form-actions"><a class="btn btn-outline" href="{{ route('admin.services') }}">Cancel</a><button class="btn btn-teal" type="submit">Save service</button></div>
    </form>
</section>
@endsection
