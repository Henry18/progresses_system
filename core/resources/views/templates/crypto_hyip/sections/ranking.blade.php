@if (gs('user_ranking'))
    @php
        $ranking = getContent('ranking.content', true);

        $userRankings = App\Models\UserRanking::active()->get();
    @endphp

    @if ($userRankings->count() > 0)
        <section class="ranking-section my-120">
            <div class="container">
                <div class="section-heading">
                    <h1 class="section-heading__title wow fadeInUp" data-wow-delay="0.1s">{{ __(@$ranking->data_values->heading) }}</h1>
                    <p class="section-heading__desc wow fadeInUp" data-wow-delay="0.2s">{{ __(@$ranking->data_values->subheading) }}</p>
                </div>
                <div class="referral__level__area wow fadeInUp" data-wow-delay="0.3s">
                    @php
                        $firstPercent = 20;
                        $lastPercent = 100;
                        $count = $userRankings->count();

                        if ($count > 1) {
                            $perItem = ($lastPercent - $firstPercent) / ($count - 1);
                        } elseif ($count == 1) {
                            $perItem = $lastPercent;
                        } else {
                            $perItem = 0;
                        }
                    @endphp

                    @foreach ($userRankings as $rank)
                        <div class="referral__level__item">
                            <div class="referral__level__item__inner">
                                <div class="referral__left">
                                    <div class="referral__level__thumb">
                                        <img src="{{ getImage(getFilePath('userRanking') . '/' . $rank->icon, getFileSize('userRanking')) }}" alt="referral">
                                    </div>
                                    <div class="referral__level__name">
                                        {{ __($rank->name) }}
                                    </div>
                                </div>
                                <div class="referral__right">
                                    <div class="referral__level__content custom-width" data-custom_width="{{ $perItem * $loop->index + $firstPercent }}">
                                        <div class="referral__level__content__content">
                                            <span><i class="las la-coins"></i> @lang('Bonus'): {{ showAmount($rank->bonus) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="referral__tooltip">
                                <ul>
                                    <li class="d-flex justify-content-between">
                                        @lang('Level')
                                        <span>{{ __($rank->level) }}</span>
                                    </li>
                                    <li class="d-flex justify-content-between">
                                        @lang('Minimum Invest')
                                        <span>{{ showAmount($rank->minimum_invest) }}</span>
                                    </li>
                                    <li class="d-flex justify-content-between">
                                        @lang('Team Invest')
                                        <span>{{ showAmount($rank->min_referral_invest) }}</span>
                                    </li>
                                    <li class="d-flex justify-content-between">
                                        @lang('No. of Direct Referral')
                                        <span>{{ $rank->min_referral }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @push('script')
        <script>
            (function($) {
                "use strict";
                $('.custom-width').each(function(index, value) {
                    $(value).css("max-width", `${$(value).data('custom_width')}%`);
                });
            })(jQuery);
        </script>
    @endpush
@endif
