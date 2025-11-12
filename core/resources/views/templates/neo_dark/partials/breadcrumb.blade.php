  <section class="inner-hero-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="inner-hero-content">
                    <h2 class="inner-hero__title text-shadow">{{__($pageTitle)}}</h2>
                </div>
            </div>
        </div>
    </div>
    <div id="particles"></div>
</section>
@push('script-lib')
<script src="{{asset(activeTemplate(true).'js/particles.min.js')}}"></script>
<script src="{{asset(activeTemplate(true).'js/colorJs.php?color='.gs('base_color'))}}"></script>
@endpush
