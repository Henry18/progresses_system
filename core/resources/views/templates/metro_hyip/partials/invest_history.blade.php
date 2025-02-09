<script>
    "use strict"

    function createCountDown(elementId, sec) {
        var tms = sec;
        var x = setInterval(function() {
            var distance = tms * 1000;
            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
            document.getElementById(elementId).innerHTML = days + "d: " + hours + "h " + minutes + "m " +
                seconds + "s ";
            if (distance < 0) {
                clearInterval(x);
                document.getElementById(elementId).innerHTML = "COMPLETE";
            }
            tms--;
        }, 1000);
    }
</script>

<div class="col-md-12">
    <div class="table-section">
        <table class="table style-two table--responsive--lg">
            <thead>
                <tr>
                    <th>@lang('Plan')</th>
                    <th>@lang('Return')</th>
                    <th>@lang('Received')</th>
                    <th class="text-center">@lang('Next payment')</th>
                    <th>@lang('Action')</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invests as $invest)
                    <tr>
                        <td>{{ __($invest->plan->name) }} <br> {{ showAmount($invest->amount) }} </td>
                        <td>
                            {{ showAmount($invest->amount*($invest->mon_interest_rate/100)) }} @lang('every') @lang('Day')
                            <br>
                            @lang('for')
                            @if ($invest->period == '-1')
                                @lang('Lifetime')
                            @else
                                {{ $invest->period }}
                                @lang('Months')
                            @endif
                            @if ($invest->capital_status == '1')
                                + @lang('Capital')
                            @endif
                        </td>
                        <td>
                            @if ($invest->compound_times)
                                {{ $invest->return_rec_time }} @lang('times') | {{ showAmount($invest->paid) }}
                            @else
                                {{ $invest->return_rec_time }}x{{ showAmount($invest->amount*($invest->mon_interest_rate/100)) }} = {{ showAmount($invest->paid) }}
                            @endif
                        </td>

                        <td scope="row" class="fw-bold text-center">
                            @if ($invest->status == '1')
                                <p id="counter{{ $invest->id }}" class="demo countdown timess2 "></p>

                                @php
                                    if ($invest->last_time) {
                                        $start = $invest->last_time;
                                    } else {
                                        $start = $invest->created_at;
                                    }
                                @endphp
                                <div class="progress">
                                    <div class="progress-bar progress-bar-striped bg--base" role="progressbar"
                                        style="width: {{ diffDatePercent($start, $invest->next_time) }}%"
                                        aria-valuenow="10" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                            @else
                                <span class="badge badge--success">@lang('Completed')</span>
                            @endif
                        </td>

                        <td>
                            <a href="{{ route('user.invest.details', encrypt($invest->id)) }}"
                                class="btn btn--base btn--sm">
                                <i class="las la-desktop"></i>
                            </a>
                            @if ($invest->eligibleCapitalBack())
                                <button class="btn btn--base btn--sm manageCapital" data-id="{{ $invest->id }}">
                                    <i class="las la-hand-holding-usd"></i>
                                </button>
                            @endif
                        </td>

                    </tr>
                    @if (\Carbon\Carbon::parse($invest->next_time) > now())
                        <script>
                            createCountDown('counter<?php echo $invest->id; ?>', {{ abs(\Carbon\Carbon::parse($invest->next_time)->diffInSeconds()) }});
                        </script>
                    @endif
                @empty
                    <tr>
                        <td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal custom--modal fade" id="capitalModal">
    <div class="modal-dialog modal-dialog-centered modal-content-bg">
        <div class="modal-content">
            <div class="modal-header">
                <strong class="modal-title text-white" id="ModalLabel">
                    @lang('Manage Invest Capital')
                </strong>
                <button type="button" data-bs-dismiss="modal">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="{{ route('user.invest.capital.manage') }}" method="post">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="invest_id">
                    <div class="form-group has-icon-select">
                        <label>@lang('Investment Capital')</label>
                        <select name="capital" class="form--control">
                            <option value="reinvest">@lang('Reinvest')</option>
                            <option value="capital_back">@lang('Capital Back')</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--base btn-md w-100">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.manageCapital').on('click', function() {
                let modal = $('#capitalModal');
                modal.find('[name=invest_id]').val($(this).data('id'));
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
