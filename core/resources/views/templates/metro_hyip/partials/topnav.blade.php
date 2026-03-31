@php
    $promotionCount = App\Models\PromotionTool::count();
@endphp
<nav class="prgr-topnav">
    <div class="prgr-topnav__inner">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="prgr-topnav__brand">
            <div class="prgr-topnav__logo-icon">
                <i class="las la-chart-line"></i>
            </div>
            <span class="prgr-topnav__logo-text">{{ strtoupper(substr(gs('site_name'), 0, 4)) }}</span>
        </a>

        {{-- Nav items --}}
        <ul class="prgr-topnav__menu d-none d-lg-flex">
            <li class="prgr-topnav__item {{ menuActive('user.home') }}">
                <a href="{{ route('user.home') }}" class="prgr-topnav__link">
                    <i class="las la-th-large"></i> @lang('Tablero')
                </a>
            </li>
            <li class="prgr-topnav__item {{ menuActive(['plan','user.invest.statistics','user.invest.log','user.invest.details']) }}">
                <a href="{{ route('user.invest.statistics') }}" class="prgr-topnav__link">
                    <i class="las la-chart-line"></i> @lang('Inversión')
                </a>
            </li>
            <li class="prgr-topnav__item {{ menuActive(['user.projects.*']) }}">
                <a href="{{ route('user.projects.index') }}" class="prgr-topnav__link">
                    <i class="las la-project-diagram"></i> @lang('Proyectos')
                </a>
            </li>
            @if(gs('staking_option'))
            <li class="prgr-topnav__item {{ menuActive('user.staking.index') }}">
                <a href="{{ route('user.staking.index') }}" class="prgr-topnav__link">
                    <i class="las la-layer-group"></i> @lang('Staking')
                </a>
            </li>
            @endif
            @if(gs('pool_option'))
            <li class="prgr-topnav__item {{ menuActive(['user.pool.index','user.pool.invests']) }}">
                <a href="{{ route('user.pool.index') }}" class="prgr-topnav__link">
                    <i class="las la-cubes"></i> @lang('Fondo')
                </a>
            </li>
            @endif
            <li class="prgr-topnav__item {{ menuActive('user.deposit.*') }}">
                <a href="{{ route('user.deposit.index') }}" class="prgr-topnav__link">
                    <i class="las la-plus-circle"></i> @lang('Depósito')
                </a>
            </li>
            <li class="prgr-topnav__item {{ menuActive(['user.withdraw','user.withdraw.history']) }}">
                <a href="{{ route('user.withdraw') }}" class="prgr-topnav__link">
                    <i class="las la-minus-circle"></i> @lang('Retirar')
                </a>
            </li>
            <li class="prgr-topnav__item {{ menuActive('user.transactions') }}">
                <a href="{{ route('user.transactions') }}" class="prgr-topnav__link">
                    <i class="las la-exchange-alt"></i> @lang('Transacciones')
                </a>
            </li>
        </ul>

        {{-- Right side --}}
        <div class="prgr-topnav__right">

            {{-- Language --}}
            @if(gs('multi_language'))
                @include($activeTemplate . 'partials.language')
            @endif

            {{-- Notification bell --}}
            <div class="prgr-topnav__bell">
                <i class="las la-bell"></i>
                <span class="prgr-topnav__bell-dot"></span>
            </div>

            {{-- User --}}
            <div class="prgr-topnav__user">
                <div class="prgr-topnav__user-info">
                    <span class="prgr-topnav__user-name">{{ auth()->user()->fullname }}</span>
                    <span class="prgr-topnav__user-email">{{ auth()->user()->email }}</span>
                </div>
                <div class="prgr-topnav__avatar">
                    {{ getInitials(auth()->user()->fullname) }}
                </div>
                <ul class="prgr-topnav__dropdown">
                    <li><a href="{{ route('user.profile.setting') }}"><i class="las la-user"></i> @lang('Perfil')</a></li>
                    <li><a href="{{ route('user.change.password') }}"><i class="las la-lock"></i> @lang('Contraseña')</a></li>
                    <li><a href="{{ route('user.transactions') }}"><i class="las la-exchange-alt"></i> @lang('Transacciones')</a></li>
                    @if(gs('user_ranking'))
                    <li><a href="{{ route('user.invest.ranking') }}"><i class="las la-crown"></i> @lang('Ranking')</a></li>
                    @endif
                    <li class="prgr-topnav__dropdown-divider"></li>
                    <li><a href="{{ route('user.logout') }}" class="text-danger"><i class="las la-sign-out-alt"></i> @lang('Cerrar sesión')</a></li>
                </ul>
            </div>

            {{-- Mobile hamburger --}}
            <button class="prgr-topnav__hamburger d-lg-none" id="pgrMobileMenuToggle">
                <i class="las la-bars"></i>
            </button>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div class="prgr-topnav__mobile" id="pgrMobileMenu">
        <ul>
            <li><a href="{{ route('user.home') }}"><i class="las la-th-large"></i> @lang('Tablero')</a></li>
            <li><a href="{{ route('user.invest.statistics') }}"><i class="las la-chart-line"></i> @lang('Inversión')</a></li>
            <li><a href="{{ route('user.projects.index') }}"><i class="las la-project-diagram"></i> @lang('Proyectos')</a></li>
            @if(gs('staking_option'))
            <li><a href="{{ route('user.staking.index') }}"><i class="las la-layer-group"></i> @lang('Staking')</a></li>
            @endif
            @if(gs('pool_option'))
            <li><a href="{{ route('user.pool.index') }}"><i class="las la-cubes"></i> @lang('Fondo')</a></li>
            @endif
            <li><a href="{{ route('user.deposit.index') }}"><i class="las la-plus-circle"></i> @lang('Depósito')</a></li>
            <li><a href="{{ route('user.withdraw') }}"><i class="las la-minus-circle"></i> @lang('Retirar')</a></li>
            <li><a href="{{ route('user.transactions') }}"><i class="las la-exchange-alt"></i> @lang('Transacciones')</a></li>
            <li><a href="{{ route('ticket.index') }}"><i class="las la-ticket-alt"></i> @lang('Soporte')</a></li>
            <li><a href="{{ route('user.logout') }}"><i class="las la-sign-out-alt"></i> @lang('Cerrar sesión')</a></li>
        </ul>
    </div>
</nav>

@push('script')
<script>
(function($){
    // User dropdown toggle
    $('.prgr-topnav__user').on('click', function(e) {
        e.stopPropagation();
        $(this).toggleClass('open');
    });
    $(document).on('click', function() {
        $('.prgr-topnav__user').removeClass('open');
    });

    // Mobile menu toggle
    $('#pgrMobileMenuToggle').on('click', function() {
        $('#pgrMobileMenu').toggleClass('open');
    });
})(jQuery);
</script>
@endpush
