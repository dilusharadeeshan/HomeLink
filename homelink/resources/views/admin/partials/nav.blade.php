<nav class="admin-nav" aria-label="Administration sections">
    <a class="{{ request()->routeIs('admin.index') ? 'active' : '' }}" href="{{ route('admin.index') }}"><x-icon name="users-round" />Accounts</a>
    <a class="{{ request()->routeIs('admin.categories*') ? 'active' : '' }}" href="{{ route('admin.categories') }}"><x-icon name="shapes" />Categories</a>
    <a class="{{ request()->routeIs('admin.services*') ? 'active' : '' }}" href="{{ route('admin.services') }}"><x-icon name="briefcase-business" />Services</a>
    <a class="{{ request()->routeIs('admin.bookings*') ? 'active' : '' }}" href="{{ route('admin.bookings') }}"><x-icon name="calendar-days" />Bookings</a>
    <a class="{{ request()->routeIs('admin.reviews*') ? 'active' : '' }}" href="{{ route('admin.reviews') }}"><x-icon name="message-square-text" />Reviews</a>
</nav>
