<!-- ==================== Header Start Here ==================== -->
<header class="header" id="header">
    <div class="container">
        <nav class="navbar navbar-expand-xl navbar-light">
            <a class="navbar-brand logo" href="{{ route('home') }}">
                <img src="{{ siteLogo() }}" alt="@lang('logo')">
            </a>
            <button class="navbar-toggler header-button" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasDarkNavbar" aria-controls="offcanvasDarkNavbar"
                aria-label="Toggle navigation">
                <span id="hiddenNav"><i class="las la-bars"></i></span>
            </button>

            <div class="offcanvas border-0 offcanvas-end" tabindex="-1" id="offcanvasDarkNavbar">
                <div class="offcanvas-header">
                    <a class="logo navbar-brand" href="{{ route('home') }}">
                        <img src="{{ siteLogo() }}" alt="@lang('logo')">
                    </a>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav nav-menu align-items-xl-center justify-content-end w-100">
                        <li class="nav-item d-xl-none">
                            <div class="flex-between gap-3">

                                @if (gs('multi_language'))
                                    @include('Template::partials.language')
                                @endif

                                <div class="auth-btn">
                                    @guest
                                        <a href="{{ route('user.login') }}" class="auth-btn-link login-btn">
                                            <span class="icon">
                                                <i class="fa-solid fa-user"></i>
                                            </span>
                                            <span class="text">@lang('Login')</span>
                                        </a>
                                        <a href="{{ route('user.register') }}" class="auth-btn-link">
                                            <span class="text">@lang('Register')</span>
                                        </a>
                                    @else
                                        <a href="{{ route('user.home') }}" class="auth-btn-link login-btn">
                                            <span class="icon">
                                                <i class="fa-solid fa-home"></i>
                                            </span>
                                            <span class="text">@lang('Dashboard')</span>
                                        </a>
                                        <a href="{{ route('user.logout') }}" class="auth-btn-link">
                                            <span class="text">@lang('Logout')</span>
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </li>

                            <li class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                                <a class="nav-link" aria-current="page" href="{{ route('home') }}">@lang('Home')</a>
                            </li>

                            @php
                                $pages = App\Models\Page::where('tempname', activeTemplate())
                                    ->where('is_default', 0)
                                    ->get();
                            @endphp
                            @foreach ($pages as $k => $data)
                                @php
                                    $isActive = request()->routeIs('pages') && request()->route('slug') == $data->slug;
                                @endphp
                                <li class="nav-item {{ $isActive ? 'active' : '' }}">
                                    <a class="nav-link"
                                        href="{{ route('pages', [$data->slug]) }}">{{ __($data->name) }}</a>
                                </li>
                            @endforeach

                            <li class="nav-item {{ request()->routeIs('plan') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('plan') }}">@lang('Plan')</a>
                            </li>
                            <li class="nav-item {{ request()->routeIs('blog*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('blogs') }}">@lang('Blog')</a>
                            </li>
                            <li class="nav-item {{ request()->routeIs('contact') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('contact') }}">@lang('Contact')</a>
                            </li>
                            <li class="nav-item d-xl-none d-block">
                                <a href="{{ route('user.login') }}" class="btn w-100 btn--md pill btn-outline--dark">
                                    <span class="icon">
                                        <i class="fa-regular fa-circle-user"></i>
                                    </span>
                                    @lang('Login')
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="header-right d-none d-xl-flex">
                    <div class="auth-btn">
                        @guest
                            <a href="{{ route('user.login') }}" class="auth-btn-link login-btn">
                                <span class="icon">
                                    <i class="fa-solid fa-user"></i>
                                </span>
                                <span class="text">@lang('Login')</span>
                            </a>
                            <a href="{{ route('user.register') }}" class="auth-btn-link">
                                <span class="text">@lang('Register')</span>
                            </a>
                        @else
                            <a href="{{ route('user.home') }}" class="auth-btn-link login-btn">
                                <span class="icon">
                                    <i class="fa-solid fa-home"></i>
                                </span>
                                <span class="text">@lang('Dashboard')</span>
                            </a>
                            <a href="{{ route('user.logout') }}" class="auth-btn-link">
                                <span class="text">@lang('Logout')</span>
                            </a>
                            @endif
                        </div>
                        <div class="dropdown lang-box">
                            @if (gs('multi_language'))
                                @include('Template::partials.language')
                            @endif
                        </div>
                    </div>
                </nav>
            </div>
        </header>
        <!-- ==================== Header End Here ==================== -->
