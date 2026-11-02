<footer class="site-footer" aria-label="HomeLink information">
    <div class="site-footer-main">
        <div class="site-footer-about">
            <a class="site-footer-brand" href="{{ route('home') }}"><span><x-icon name="house" /></span>HomeLink<span class="site-footer-dot">.</span></a>
            <p>Find local home services, manage your bookings, and keep every update in one place.</p>
        </div>
        <nav class="site-footer-nav" aria-label="Explore HomeLink">
            <h2>Explore</h2>
            <a href="{{ route('home') }}">Browse services</a>
            <a href="{{ route('how') }}">How it works</a>
        </nav>
        <nav class="site-footer-nav" aria-label="Your account">
            <h2>Your account</h2>
            @auth
                @if(auth()->user()->role === 'provider')
                    <a href="{{ route('bookings.index') }}">Booking requests</a>
                    <a href="{{ route('provider.services') }}">My services</a>
                @elseif(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.index') }}">Admin workspace</a>
                    <a href="{{ route('admin.bookings') }}">Manage bookings</a>
                @else
                    <a href="{{ route('bookings.index') }}">My bookings</a>
                    <a href="{{ route('profile') }}">My profile</a>
                @endif
            @else
                <a href="{{ route('login') }}">Log in</a>
                <a href="{{ route('register') }}">Create an account</a>
            @endauth
        </nav>
    </div>
    <div class="site-footer-bottom">
        <span>© {{ date('Y') }} HomeLink · Sri Lanka</span>
        <span>{{ config('homelink.demo') ? 'Demo workspace · fictional data' : 'Good people. Better homes.' }}</span>
    </div>
</footer>
