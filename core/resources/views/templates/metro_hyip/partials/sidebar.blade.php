@php
    $promotionCount = App\Models\PromotionTool::count();
@endphp

<div class="sidebar-menu">
    <span class="sidebar-menu__close d-xl-none d-block"><i class="las la-times"></i></span>
    <!-- Sidebar Logo Start -->
    <div class="sidebar-logo">
        <a href="{{ route('home') }}" class="sidebar-logo__link"> <img src="{{ siteLogo() }}" alt="site-logo"></a>
    </div>
    <!-- Sidebar Logo End -->
   
    <!-- ========= Sidebar Menu Start ================ -->
    <ul class="sidebar-menu-list mt-5">
        <li class="sidebar-menu-list__item {{ menuActive('user.home') }} ">
            <a href="{{ route('user.home') }}" class="sidebar-menu-list__link ">
                <span class="icon"><i class="fas fa-tachometer-alt"></i></span>
                <span class="text">@lang('Dashboard')</span>
            </a>
        </li>
        <li class="sidebar-menu-list__item {{ menuActive(['plan', 'user.invest.statistics', 'user.invest.log', 'user.invest.details']) }} ">
            <a href="{{ route('plan') }}" class="sidebar-menu-list__link ">
                <span class="icon"><i class="fas fa-funnel-dollar"></i></span>
                <span class="text">@lang('Investment')</span>
            </a>
        </li>
        @if (gs('schedule_invest'))
            <li class="sidebar-menu-list__item {{ menuActive('user.invest.schedule') }} ">
                <a href="{{ route('user.invest.schedule') }}" class="sidebar-menu-list__link ">
                    <span class="icon"><i class="fas fa-calendar-check"></i></span>
                    <span class="text">@lang('Schedule Investment')</span>
                </a>
            </li>
        @endif
        @if (gs('staking_option')) 
            <li class="sidebar-menu-list__item {{ menuActive('user.staking.index') }} ">
                <a href="{{ route('user.staking.index') }}" class="sidebar-menu-list__link ">
                    <span class="icon"><i class="fas fa-chart-line"></i></span>
                    <span class="text">@lang('Staking')</span>
                </a>
            </li>
        @endif
        @if (gs('pool_option')) 
            <li class="sidebar-menu-list__item {{ menuActive(['user.pool.index', 'user.pool.invests']) }} ">
                <a href="{{ route('user.pool.index') }}" class="sidebar-menu-list__link ">
                    <span class="icon"><i class="fas fa-cubes"></i></span>
                    <span class="text">@lang('Pool')</span>
                </a>
            </li>
        @endif
        <li class="sidebar-menu-list__item has-dropdown {{ menuActive('user.deposit.*') }}">
            <a href="javascript:void(0)" class="sidebar-menu-list__link">
                <span class="icon"><i class="fas fa-wallet"></i></span>
                <span class="text">@lang('Deposit')</span>
            </a>
            <div class="sidebar-submenu">
                <ul class="sidebar-submenu-list">
                    <li class="sidebar-submenu-list__item {{ menuActive('user.deposit.index') }}">
                        <a href="{{ route('user.deposit.index') }}" class="sidebar-submenu-list__link">@lang('Deposit Now')</a>
                    </li>
                    <li class="sidebar-submenu-list__item {{ menuActive('user.deposit.history') }}">
                        <a href="{{ route('user.deposit.history') }}" class="sidebar-submenu-list__link">@lang('Deposit History')</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="sidebar-menu-list__item has-dropdown {{ menuActive(['user.withdraw', 'user.withdraw.history']) }}">
            <a href="javascript:void(0)" class="sidebar-menu-list__link">
                <span class="icon"><i class="fas fa-hand-holding-usd"></i></span>
                <span class="text">@lang('Withdraw')</span>
            </a>
            <div class="sidebar-submenu {{ menuActive(['user.withdraw', 'user.withdraw.history']) }}">
                <ul class="sidebar-submenu-list">
                    <li class="sidebar-submenu-list__item {{ menuActive('user.withdraw') }}">
                        <a href="{{ route('user.withdraw') }}" class="sidebar-submenu-list__link">@lang('Withdraw Now')</a>
                    </li>
                    <li class="sidebar-submenu-list__item {{ menuActive('user.withdraw.history') }}">
                        <a href="{{ route('user.withdraw.history') }}" class="sidebar-submenu-list__link">@lang('Withdraw History')</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="sidebar-menu-list__item has-dropdown">
            <a href="javascript:void(0)" class="sidebar-menu-list__link">
                <span class="icon"><i class="fas fa-money-bill-wave"></i></span>
                <span class="text">@lang('Token')</span>
            </a>
            <div class="sidebar-submenu {{ menuActive(['user.token', 'user.token.grafica']) }}">
                <ul class="sidebar-submenu-list">
                    <li class="sidebar-submenu-list__item  {{ menuActive('user.token.grafica') }}">
                        <a href="{{ route('user.token.grafica') }}" class="sidebar-submenu-list__link">Grafica</a>
                    </li>
                    <li class="sidebar-submenu-list__item ">
                        <a target="_blank" href="https://pancakeswap.finance/?inputCurrency=0x55d398326f99059fF775485246999027B3197955&outputCurrency=0x2e954e60ea4125EfaBda0F0bdedF1a68d6040E23" class="sidebar-submenu-list__link">Comprar Token</a>
                    </li>
                </ul>
            </div>
        </li>
        @if (gs('b_transfer'))
            <li class="sidebar-menu-list__item {{ menuActive('user.transfer.balance') }}">
                <a href="{{ route('user.transfer.balance') }}" class="sidebar-menu-list__link">
                    <span class="icon"><i class="fas fa-handshake"></i></span>
                    <span class="text"> @lang('Transfer Balance') </span>
                </a>
            </li>
        @endif
        
        <li class="sidebar-menu-list__item {{ menuActive('user.transactions') }}">
            <a href="{{ route('user.transactions') }}" class="sidebar-menu-list__link">
                <span class="icon"> <i class="fas fa-exchange-alt"></i> </span>
                <span class="text"> @lang('Transactions') </span>
            </a>
        </li>
        <li class="sidebar-menu-list__item {{ menuActive('user.referrals') }}">
            <a href="{{ route('user.referrals') }}" class="sidebar-menu-list__link">
                <span class="icon"> <i class="fas fa-users"></i> </span>
                <span class="text"> @lang('Referrals') </span>
            </a>
        </li>

        @if (gs('promotional_tool') && $promotionCount)
            <li class="sidebar-menu-list__item {{ menuActive('user.promotional.banner') }}">
                <a href="{{ route('user.promotional.banner') }}" class="sidebar-menu-list__link">
                    <span class="icon"><i class="las la-ad"></i></span>
                    <span class="text"> @lang('Promotional Tool')</span>
                </a>
            </li>
        @endif

        @if (gs('user_ranking'))
            <li class="sidebar-menu-list__item {{ menuActive('user.invest.ranking') }}">
                <a href="{{ route('user.invest.ranking') }}" class="sidebar-menu-list__link">
                    <span class="icon"><i class="las la-crown"></i></span>
                    <span class="text"> @lang('Ranking')</span>
                </a>
            </li>
        @endif

        <li class="sidebar-menu-list__item has-dropdown {{ menuActive(['ticket.index', 'ticket.open']) }}">
            <a href="javascript:void(0)" class="sidebar-menu-list__link">
                <span class="icon"><i class="fas fa-ticket-alt"></i></span>
                <span class="text">@lang('Support Ticket')</span>
            </a>
            <div class="sidebar-submenu {{ menuActive(['ticket.index', 'ticket.open']) }}">
                <ul class="sidebar-submenu-list">
                    <li class="sidebar-submenu-list__item active">
                        <a href="{{ route('ticket.open') }}" class="sidebar-submenu-list__link">@lang('Open Ticket')</a>
                    </li>
                    <li class="sidebar-submenu-list__item">
                        <a href="{{ route('ticket.index') }}" class="sidebar-submenu-list__link">@lang('My Tickets')</a>
                    </li>

                </ul>
            </div>
        </li>
        <li class="sidebar-menu-list__item {{ menuActive('user.twofactor') }}">
            <a href="{{ route('user.twofactor') }}" class="sidebar-menu-list__link">
                <span class="icon"><i class="fas fa-lock"></i></span>
                <span class="text"> @lang('2FA Security') </span>
            </a>
        </li>
        <li class="sidebar-menu-list__item">
            <a href="{{ route('user.logout') }}" class="sidebar-menu-list__link">
                <span class="icon"><i class="fas fa-sign-out-alt"></i></span>
                <span class="text">@lang('Logout')</span>
            </a>
        </li>
    </ul>

    <div class="bottom-footer py-3">
        <div class="container">
            <div class="row gy-3">
                <div class="col-md-12 text-center">
                    <div class="bottom-footer-text text-white"> &copy; {{ date('Y') }}
                        <a href="{{ route('home') }}">{{ __(gs('site_name')) }}</a> @lang('All Rights Reserved').
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ========= Sidebar Menu End ================ -->
</div>
