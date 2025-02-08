<link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/frontend-lite.min.css') }}">
<link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/wfeature.min.css') }}">
<link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/post-1717.css') }}">
@php
    $inversiones = getContent('inversiones.content', true);
@endphp
<section class="elementor-section elementor-top-section elementor-element elementor-element-27de702 elementor-section-boxed elementor-section-height-default elementor-section-height-default">
    <div class="elementor-container elementor-column-gap-default">
	<div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-7dd5cc0">
		<div class="elementor-widget-wrap elementor-element-populated">
			<div class="elementor-element elementor-element-3785f12 pakit-star-rating--align-center elementor-widget elementor-widget-witr_section_title">
				<div class="elementor-widget-container">
					<div class="witr_section_title">
						<div class="witr_section_title_inner text-center">
							<h2><font style="vertical-align:inherit;"><font style="vertical-align:inherit;">{{ __(@$inversiones->data_values->heading) }}</font></font></h2>
							<div class="witr_bar_main">
								<div class="witr_bar_inner"></div>
							</div>
							<h3><font style="vertical-align:inherit;"><font style="vertical-align:inherit;">{{ __(@$inversiones->data_values->sub_heading) }}</font></font></h3></div>
					</div>
				</div>
			</div>
			<div class="elementor-container elementor-column-gap-default">
				<div class="elementor-column elementor-col-33 elementor-inner-column elementor-element elementor-element-2605275">
					<div class="elementor-widget-wrap elementor-element-populated">
						<div class="elementor-element elementor-element-2e78c01 pakit-star-rating--align-center elementor-widget elementor-widget-witr_section_feature">
							<div class="elementor-widget-container">
								<div class="sub-border-2 all_feature_color text-center">
									<div class="sub-item width_height_link_0">                
                    <img width="96" height="120" src="{{ frontendImage('inversiones', @$inversiones->data_values->image1,'600x580') }}" class="attachment-large size-large wp-image-18634" alt="">
										<h3><font style="vertical-align:inherit;"><font style="vertical-align:inherit;">{{ __(@$inversiones->data_values->label_point_one) }}</font></font></h3>
										<p>
											<font style="vertical-align:inherit;">
												<font style="vertical-align:inherit;">{{ __(@$inversiones->data_values->sub_label_point_one) }}</font>
											</font>
										</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="elementor-column elementor-col-33 elementor-inner-column elementor-element elementor-element-420f6de">
					<div class="elementor-widget-wrap elementor-element-populated">
						<div class="elementor-element elementor-element-8ad84cb pakit-star-rating--align-center elementor-widget elementor-widget-witr_section_feature">
							<div class="elementor-widget-container">
								<div class="sub-border-2 all_feature_color text-center">
									<div class="sub-item width_height_link_0">
                  <img width="96" height="129" src="{{ frontendImage('inversiones', @$inversiones->data_values->image2,'600x580') }}" class="attachment-full size-full wp-image-18661" alt="">
										<h3><font style="vertical-align:inherit;"><font style="vertical-align:inherit;">{{ __(@$inversiones->data_values->label_point_two) }}</font></font></h3><font style="vertical-align:inherit;">
										</font><p>
											<font style="vertical-align:inherit;">
												<font style="vertical-align:inherit;">{{ __(@$inversiones->data_values->sub_label_point_two) }}</font>
											</font>
										</p>
									</div><font style="vertical-align:inherit;">
								</font></div><font style="vertical-align:inherit;">
							</font></div><font style="vertical-align:inherit;">
						</font></div><font style="vertical-align:inherit;">
					</font></div><font style="vertical-align:inherit;">
				</font></div><font style="vertical-align:inherit;">
				</font><div class="elementor-column elementor-col-33 elementor-inner-column elementor-element elementor-element-d51a882">
					<div class="elementor-widget-wrap elementor-element-populated">
						<div class="elementor-element elementor-element-26cc42e pakit-star-rating--align-center elementor-widget elementor-widget-witr_section_feature">
							<div class="elementor-widget-container">
								<div class="sub-border-2 all_feature_color text-center">
									<div class="sub-item width_height_link_0">
                  <img width="96" height="120" src="{{ frontendImage('inversiones', @$inversiones->data_values->image3,'600x580') }}" class="attachment-large size-large wp-image-18662" alt="">
										<h3><font style="vertical-align:inherit;"><font style="vertical-align:inherit;">{{ __(@$inversiones->data_values->label_point_three) }}</font></font></h3><font style="vertical-align:inherit;">
										</font><p>
											<font style="vertical-align:inherit;">
												<font style="vertical-align:inherit;">{{ __(@$inversiones->data_values->sub_label_point_three) }}</font>
											</font>
										</p>
									</div><font style="vertical-align:inherit;">
								</font></div><font style="vertical-align:inherit;">
							</font></div><font style="vertical-align:inherit;">
						</font></div><font style="vertical-align:inherit;">
					</font></div><font style="vertical-align:inherit;">
				</font></div><font style="vertical-align:inherit;">
			</font></div><font style="vertical-align:inherit;">

		</font></div><font style="vertical-align:inherit;">
	</font></div><font style="vertical-align:inherit;">
</font></div></section>