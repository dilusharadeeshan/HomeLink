@extends('layouts.app')
@section('title', 'Manage categories')
@section('eyebrow', 'Administration')
@section('content')
<div class="page-heading"><div><p class="eyebrow">CATALOG STRUCTURE</p><h1>Service categories</h1><p>Create and maintain the categories providers use for their listings.</p></div></div>
@include('admin.partials.nav')
<section class="panel">
    <div class="section-heading"><h2>Add a category</h2></div>
    <form method="POST" action="{{ route('admin.categories.store') }}" class="admin-category-create">@csrf
        <label class="field">Name<input name="name" maxlength="80" value="{{ old('name') }}" required placeholder="e.g. Home repairs"></label>
        <label class="field">Icon<select name="icon" required>@foreach($icons as $icon)<option value="{{ $icon }}" @selected(old('icon') === $icon)>{{ str($icon)->replace('-', ' ')->title() }}</option>@endforeach</select></label>
        <label class="field">Theme color<select name="color" required>@foreach($colors as $color)<option value="{{ $color }}" @selected(old('color') === $color)>{{ ucfirst($color) }}</option>@endforeach</select></label>
        <button class="btn btn-teal" type="submit">Add category</button>
    </form>
</section>
<section class="panel">
    <div class="section-heading"><h2>Categories</h2><span class="muted">{{ $categories->total() }} total</span></div>
    <div class="table-wrap"><table><thead><tr><th>Category</th><th>Slug</th><th>Listings</th><th>Update</th><th>Remove</th></tr></thead><tbody>
    @forelse($categories as $category)
        <tr>
            <td><span class="category-admin-label"><span class="category-icon color-{{ $category->color }}"><x-icon :name="$category->icon" /></span>{{ $category->name }}</span></td>
            <td><code>{{ $category->slug }}</code></td>
            <td>{{ $category->listings_count }}</td>
            <td><form method="POST" action="{{ route('admin.categories.update', $category) }}" class="admin-category-edit">@csrf @method('PUT')
                <label class="sr-only" for="name-{{ $category->id }}">Category name</label><input id="name-{{ $category->id }}" name="name" value="{{ $category->name }}" maxlength="80" required>
                <label class="sr-only" for="icon-{{ $category->id }}">Category icon</label><select id="icon-{{ $category->id }}" name="icon">@foreach($icons as $icon)<option value="{{ $icon }}" @selected($category->icon === $icon)>{{ str($icon)->replace('-', ' ')->title() }}</option>@endforeach</select>
                <label class="sr-only" for="color-{{ $category->id }}">Category color</label><select id="color-{{ $category->id }}" name="color">@foreach($colors as $color)<option value="{{ $color }}" @selected($category->color === $color)>{{ ucfirst($color) }}</option>@endforeach</select>
                <button class="text-button teal">Save</button>
            </form></td>
            <td><form method="POST" action="{{ route('admin.categories.destroy', $category) }}" data-confirm="Delete the {{ $category->name }} category? A category cannot be deleted while any service listing, including a removed listing, refers to it.">@csrf @method('DELETE')<button class="text-button danger" @disabled($category->listings_count > 0) title="{{ $category->listings_count > 0 ? 'A service listing refers to this category.' : 'Delete this category.' }}">Delete</button></form></td>
        </tr>
    @empty
        <tr><td colspan="5">No categories yet. Add one above.</td></tr>
    @endforelse
    </tbody></table></div>
    <div class="pagination">{{ $categories->links() }}</div>
</section>
@endsection
