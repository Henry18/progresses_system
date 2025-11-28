@php
    $user = auth()->user();
@endphp

<!-- Dashboard Header Start -->
<div class="dashboard-header">
    <div class="container-fluid">
        <div class="dashboard-header-wrapper">
            <div class="dashboard-header-left">
                <div class="d-xl-none">
                    <button class="navigation-bar">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
                <h4 class="dashboard-header-title">{{ __($pageTitle) }}</h4>
            </div>
            <div class="dashboard-header-right">
                <div class="d-none d-xl-block">
                    <div class="dropdown user-dropdown">
                        <button class="lang-box-btn w-100" data-bs-toggle="dropdown">
                            <span class="user-info">
                                <span class="user-info-wrapper">
                                    <span class="user-info-thumb">
                                        <i class="fas fa-user-circle fa-3x"></i>
                                    </span>
                                    <span class="user-info-content">
                                        <span class="name">{{ $user->username }}</span>
                                        <span class="phone">{{ $user->email }}</span>
                                    </span>
                                </span>
                            </span>
                        </button>
                        <ul class="dropdown-menu">
                            <li class="user-dropdown-item">
                                <a href="{{ route('user.profile.setting') }}" class="user-dropdown-link">
                                    <span class="icon">
                                        <i class="fa-regular fa-user"></i>
                                    </span>
                                    <span class="text">@lang('Profile')</span>
                                </a>
                            </li>
                            <li class="user-dropdown-item">
                                <a href="{{ route('ticket.index') }}" class="user-dropdown-link">
                                    <span class="icon">
                                        <i class="fa-solid fa-headphones"></i>
                                    </span>
                                    <span class="text">@lang('Support Ticket')</span>
                                </a>
                            </li>
                            <li class="devide"></li>
                            <li class="user-dropdown-item">
                                <a href="{{ route('user.logout') }}" class="user-dropdown-link">
                                    <span class="icon">
                                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                                    </span>
                                    <span class="text">@lang('Logout')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Dashboard Header End -->