jQuery(document).ready(function ($) {

	var photoswipe = false;
	var magnific = false;
	var $window = $(window);

	//initialize sliders

	var intCounter = 0;//guard against no sliders
	var slickInt = setInterval(init_sliders, 200);

	function init_sliders() {

		var sliders = $('.iki-slick-slider');
		intCounter++;

		if (sliders.length && $.fn.slick) {

			setTimeout(function () {
				try {
					sliders.slick()
				}
				catch (error) {
					//silence
				}
				clearInterval(slickInt);

			}, 0);
		}
		if (slickInt && intCounter > 20) {
			clearInterval(slickInt);
		}
	}

	$window.on('iki_theme_exports_available', function () {
		photoswipe = window.ikiThemeExports.theme.photoswipe_active;
		magnific = !ikiThemeExports.theme.photoswipe_active;
	});

	var MutationObserver = window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver;

	if (MutationObserver) {

		var observer = new MutationObserver(function (mutations) {
			mutations.forEach(function (mutation) {
				if (mutation.type === 'childList') {
					// console.log('m: ', mutation);
					if (mutation.addedNodes) {
						_.each(mutation.addedNodes, function (a) {
							var $node = $(a);
							if (a.classList.contains('vc_iki_post_slider_vc')) {
								var $slider = $node.find('.iki-slick-slider');
								if ($slider.length) {
									$slider.slick();
								}
							}
							else if (a.classList.contains('vc_iki_image_slider_vc')) {
								var $sliderImg = $node.find('.iki-slick-slider');
								var $sliderWrap = $node.children('iki-slider-click-lightbox');
								if ($sliderImg.length) {
									if ($sliderWrap) {
										if (photoswipe) {
											window.ikiThemes.Photoswipe.initPortfolio($('.iki-img-slider-vc'));
										}
										else if (magnific) {
											window.ikiThemes.LightboxInitializer.init();
										}
									}
									$sliderImg.slick();
								}
							}

							// image grid
							else if (a.classList.contains('vc_iki_image_grid_vc')) {

								if (photoswipe) {
									window.ikiThemes.Photoswipe.initPortfolio($('.iki-grid-img-vc'));
								}
								else if (magnific) {
									window.ikiThemes.LightboxInitializer.init();
								}
							}

						})
					}
				}
			});
		});

		//front end wpbakery page builder edit slider
		var $slickSliders = $('.iki-slick-slider');

		$slickSliders.each(function (index, el) {
			var $slickWrap = $(this).parents('.vc_element-container.ui-sortable');

			if ($slickWrap[0]) {
				observer.observe($slickWrap[0], {
					childList: true
				});
			}
		});


		var $imageGrids = $('.iki-grid-img-vc.iki-grid-vc-click-lightbox');
		$imageGrids.each(function (index, el) {
			var $imageGrid = $(this).parents('.vc_element-container.ui-sortable');

			if ($imageGrid[0]) {
				observer.observe($imageGrid[0], {
					childList: true
				});
			}
		});


	}
});