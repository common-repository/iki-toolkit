window.ikiToolkit = window.ikiToolkit || {};
window.ikiToolkit = window.ikiToolkit || {};
window.ikiToolkit.formHandler = (function ($, w) {

	"use strict";
	return {
		init: init,
		_getFormSection: _getFormSection,
		_setupSectionError: _setupSectionError,
		_removeSectionError: _removeSectionError,
		_setupInProgress: _setupInProgress,
		_setupSuccess: _setupSuccess,
		_setupDefault: _setupDefault,
		_animateErrors: _animateErrors

	};

	function _animateErrors() {

		if ($.fn.velocity) {
			this.errorElements.velocity('callout.shake', {stagger: 100});
		}
		this.errorElements.length = 0;
	}


	function _setupSuccess() {

		var _self = this;
		this.$submitBtn.removeClass('iki-in-progress');
		this.$notifText.text(this.$submitBtn.data('ikiSuccess'));
		this.ajaxInProgress = false;
		if ($.fn.velocity) {
			this.$submitBtn.velocity('callout.pulse');
		}
		_.delay(function () {
			_self._setupDefault();
		}, 6000);
	}

	function _setupDefault() {

		this.$notifText.text(this.$submitBtn.data('ikiDefault'));
		this.ajaxInProgress = false;
		this.$submitBtn.removeClass('iki-in-progress');

	}

	function _setupInProgress() {
		this.$submitBtn.addClass('iki-in-progress');
		this.$submitBtn.find('.iki-notif-text').text(this.$submitBtn.data('ikiProgress'));
		this.ajaxInProgress = true;
	}

	function _removeSectionError($inputElement) {
		if ($inputElement.data('ikiHasError')) {
			// when focused , remove the error notification
			var name = $inputElement.attr('name').trim();
			this.$form.find('.section-' + name)
				.removeClass('iki-form-error')
				.find('.iki-empty-notification').remove();
			$inputElement.data('ikiHasError', false);
		}
	}

	function _setupSectionError($inputElement) {

		var $section = this._getFormSection($inputElement);
		$section.addClass('iki-form-error');
		this.errorElements = this.errorElements.add($section);
		if (!$inputElement.data('ikiHasError')) {
			var text = this.emptyNotifText;
			if ($inputElement.attr('id') === 'iki-contact-answer') {
				text = this.wrongAnswerText;
			}

			$inputElement.siblings('label').append('<span class="iki-empty-notification">' + text + '</span>');
		}
		$inputElement.data('ikiHasError', true);
	}

	function _getFormSection($elem) {
		var name = $elem.attr('name').trim();
		return this.$form.find('.section-' + name);
	}

	function init(form, actionUrl) {


		var _self = this;
		this.$form = form;

		this.$submitBtn = this.$form.find('.iki-form-btn');

		this.$notifText = this.$submitBtn.find('.iki-notif-text');
		this.ajaxInProgress = false;
		this.errorElements = $([]);


		var answer = this.$form.find('.section-iki-contact-answer').data('ikiAnswer');

		if (answer) {
			answer = String(answer).trim().toLocaleLowerCase();
		}

		this.answer = answer;

		this.$formFields = this.$form.find('.iki-form-element');
		this.$gdprCbx = $('#iki-gdpr-checkbox');
		this.$gdprLabel = this.$form.find('.iki-gdpr-label');
		this.emptyNotifText = _self.$form.attr('data-iki-empty');
		this.wrongAnswerText = _self.$form.attr('data-iki-wrong-a');
		this.$notificationPanel = $(".iki-notification-panel");

		this.$answerField = this.$form.find('[name=iki-contact-answer]');
		this.$formFields.on('focus', function () {
			var $this = $(this);
			_self._removeSectionError($this);
		});

		this.$requiredFields = this.$formFields.filter('[required]');


		if (this.$gdprCbx.length) {

			this.$submitBtn.attr('disabled', true);

			this.$gdprLabel.on('click', function (e) {
				_self.$submitBtn.attr('disabled', _self.$gdprCbx[0].checked);

			});

			this.$gdprCbx.on('click', function () {

				_self.$submitBtn.attr('disabled', !_self.$gdprCbx[0].checked);
			})
		}

		this.$submitBtn.on('click', function (e) {

				e.preventDefault();

				if (!_self.ajaxInProgress) {


					var errors = false;
					_self.$requiredFields.each(function () {
						var $this = $(this);
						var v = $this.val().trim();
						if (!v) {
							//mark the field (can't be empty)
							errors = true;

							_self._setupSectionError($this);


						}
					});


					// check if there is question/ answers
					if (_self.$answerField.length) {
						var answerVal = _self.$answerField.val().trim().toLocaleLowerCase();
						if (answerVal !== _self.answer) {
							errors = true;
							_self._setupSectionError(_self.$answerField);
						}
					}

					if (!errors) {
						_self.$form.submit();
						//start progress animation
						_self.$notificationPanel.empty();
						_.defer(function () {
							_self._setupInProgress();
						});
					} else {
						_self._animateErrors();

					}
				}
			}
		);


		this.actionUrl = actionUrl;


		this.$form.on('submit', function (e) {

			e.preventDefault();

			var formData = _self.$form.serialize();

			$.ajax({
				type: 'POST',
				url: _self.actionUrl,
				timeout: 20000,
				data: {
					action: 'iki_toolkit_validate_contact_form',
					formData: formData,
					_ajax_nonce: window.ikiToolkitExports.iki_nonce,
					postId: window.ikiToolkitExports.post_id
				}
			}).done(function (data, status, xhr) {

				if (data.errors || !data.success) {
					if (data.notification) {
						//show notification
						_self.$notificationPanel
							.addClass('iki-form-error')
							.append('<p>' + data.notification + '</p>')
					}

					//we have errors
					//setup backend notification errors
					_.each(data.errors, function (value, key) {

						var htmlText = $('<p class="iki-error-field">' + key + ' : ' + value + '</p>');
						_self.$notificationPanel.append(htmlText);

						var $fieldError = _self.$form.find('[name=' + key + ']');
						if ($fieldError.length) {
							_self._setupSectionError($fieldError);
						}


					});
					_self._animateErrors();
					_self.$notificationPanel.slideDown('slow', function () {
							_.delay(function () {
								_self.$notificationPanel.slideUp('slow', function () {
										_self.$notificationPanel.empty();
									}
								);
							}, 10000);
						}
					);

					_self._setupDefault();
				} else {
					//success
					_self._setupSuccess();
				}
			})
		});
	}
})(jQuery, window);


jQuery(document).ready(function ($) {

	$('.iki-slick-slider').slick();

	if (window.ikiToolkitExports.fs_panels) {
		window.ikiToolkitExports.fs_panels.forEach(function (panelData) {

			// create js panel.
			var panel = Object.create(window.ikiToolkit.AbstractPanel);
			panel.init(panelData, window.ikiToolkitExports.flags.wp_customizer_active);
		});

	}

	//initialize hero section
	var $heroSection = $('#iki-hero-section');
	if ($heroSection.length) {
		Object.create(window.ikiToolkit.heroSection.heroContent).init();
	}

	//setup share buttons
	var $shareButtons = $('.post-sharing .iki-share-btn');
	$shareButtons.on('click', function (e) {
		if ($(window).width() > 700) {
			e.preventDefault();
			window.open(this.href, 'share-this', 'height=500,width=500,status=no,toolbar=no');
		}
	});

	// initialize contact form
	var $contactForm = $('#iki-contact-form');
	if ($contactForm.length) {

		Object.create(window.ikiToolkit.formHandler).init($contactForm, window.ikiToolkitExports.adminUrl);

	}
});
window.ikiToolkit = window.ikiToolkit || {};
window.ikiToolkit.activePanels = {};
window.ikiToolkit.AbstractPanel = (function ($, w) {

	"use strict";

	return {

		init: init,
		open: _open,
		close: _close,
		_keyDownClose: _keyDownClose,
		_onOpenComplete: _onOpenComplete,
		_onOpenBegin: _onOpenBegin,
		_onCloseComplete: _onCloseComplete
	};

	function init(options, stopPropagation) {

		this.defaults = {
			animationIn: 'transition.slideUpIn',
			animationOut: 'transition.slideDownOut',
			btnAnimation: false,
			roundPosition: false,
			toggleClass: 'iki-offscreen'
		};

		var _self = this;
		this.customElement = null;
		this.stopPropagation = stopPropagation;
		this.options = $.extend({}, this.defaults, options);
		this.$body = $('body');
		this.$html = $('html');
		this.$w = $(w);
		this.$target = $('#' + this.options.name);
		window.ikiToolkit.activePanels[this.options.name] = this;
		this.$w = $(window).on('keydown', _keyDownClose.bind(this));

		this.$closeBtn = this.$target.find('.close-btn-wrap');

		this._closeBinded = this.close.bind(this);
		this.openClass = '.' + this.options.name + '-open';
		this.$closeBtn.on('click', function () {

			var $this = $(this);
			if ($this.tooltipster) {
				$this.tooltipster('close');
			}
			_self._closeBinded();
		});

		this.revSliders = this.$target.find('.rev_slider');
		if (this.revSliders.length) {
			this.revSliders.revpause();
		}

		this.$w.on('iki_mobile_menu_init', function (e, menuEl) {
			$(menuEl).find(_self.openClass).on('click', launchPanel);
		});

		// setup custom element
		if (this.options.blurBg) {

			var imgUrl = this.$target.css('background-image');

			if (imgUrl && 'none' !== imgUrl) {

				imgUrl = imgUrl.replace('url("', '').replace('")', '');

				var $imageLoader = $('<img class="sr-only">').attr({src: imgUrl});

				var $canvas = $('<canvas class="iki-fs-panel-canvas" id="' + this.options.name + '-canvas">');

				$imageLoader.prependTo(_self.$target);

				$imageLoader.imagesLoaded()
					.done(function () {

						if (StackBlur) {
							StackBlur.image($imageLoader[0], $canvas[0], 15);
						}

						$canvas.css({
							width: '',
							height: ''
						});

						_.defer(function () {

							$imageLoader.remove();
							_self.$target.prepend($canvas);

						})

					});
			}
		}

		if (this.options.customElement) {

			if (typeof window.ikiToolkit.panelCustomBlocks[this.options.customElement.name] !== 'undefined') {

				_self.customElement = Object.create(window.ikiToolkit.panelCustomBlocks[this.options.customElement.name]);
				_self.customElement.init(_self, this.options.customElement);

			}

		}

		function launchPanel(e) {

			e.preventDefault();

			if (!_self.isOpen) {
				var $this = $(this);
				if ($this.tooltipster) {
					try {

						$this.tooltipster('close');
					}
					catch (error) {

					}
				}
				if (_self.stopPropagation) {
					e.stopImmediatePropagation();
				}
				_self.open();
			}
		}

		$(_self.openClass).on('click', launchPanel);

		$(this.options.name + '-close').on('click', function (e) {

			e.preventDefault();
			if (_self.isOpen) {
				var $this = $(this);
				if ($this.tooltipster) {
					$this.tooltipster('close');
				}

				if (_self.stopPropagation) {
					e.stopImmediatePropagation();
				}

				_self.close();
			}
		});

		this.$body.on('iki_fs_panel_open', function (event, name) {

			if (name === _self.options.name) {
				if (!_self.isAnimating) {
					_self.open();
				}
			}

		});

		this.$body.on('iki_fs_panel_close', function (event, name) {

			if (name === _self.options.name) {
				if (_self.isOpen) {
					_self.close();
				}
			}

		});
	}

	function _keyDownClose(e) {

		var code = e.keyCode || e.which;
		if (code === 27 && this.isOpen) //esc key  AND this is active
		{
			this.close();
		}
	}


	function _onCloseComplete(deferred) {

		var _self = this;
		_self.$body.removeClass('iki-fs-panel-active');
		_self.isOpen = false;
		_self.isAnimating = false;
		_self.$target.addClass(_self.options.toggleClass)
			.css('transform', '');
		_self.$html.css({marginRight: '', overflow: ''});

		if (_self.revSliders.length) {
			w.requestAnimationFrame(function () {
				try {
					this.revSliders.revpause();
				} catch (error) {
				}
			});

		}
		if (_self.customElement) {
			_self.customElement.onClose();
		}
		deferred.resolve();
	}

	function _close(e) {

		var _self = this;
		var deferred = $.Deferred();

		if (e && _self.stopPropagation) {
			e.stopImmediatePropagation();
		}

		if (this.isOpen) {

			_self.isAnimating = true;
			if ($.fn.velocity) {

				_self.$target.velocity(_self.options.animationOut, {
					duration: 500,// temp za sada mozda promeniti...
					display: 'block',
					complete: function () {
						_self._onCloseComplete(deferred);
					}
				});
			} else {
				_self.$target.css('opacity', 0);
				_.defer(function () {

					_self._onCloseComplete(deferred);
				});
			}

		}
		else {
			deferred.reject();
		}

		return deferred.promise();
	}

	function _onOpenComplete(deferred) {

		var _self = this;
		_self.isOpen = true;
		_self.openFirstTime = true;
		_self.$target.css('transform', '');
		_self.isAnimating = false;
		if (_self.revSliders.length) {
			w.requestAnimationFrame(function () {
				jQuery(window).trigger("resize");
				try {
					this.revSliders.revresume();
				} catch (error) {
				}
			});

		}

		if (_self.customElement) {
			_self.customElement.onOpen();
		}

		deferred.resolve();
	}


	function _onOpenBegin() {
		var _self = this;
		_self.isAnimating = true;
		if (_self.revSliders.length) {
			w.requestAnimationFrame(function () {
				jQuery(window).trigger("resize");
			});
		}
	}

	function _open() {

		var deferred = $.Deferred();

		var _self = this;

		this.$body.addClass('iki-fs-panel-active');
		_self.$target.removeClass(_self.options.toggleClass);

		if ($.fn.velocity) {
			_self.$target.velocity(_self.options.animationIn, {
				duration: 700,
				display: 'block',
				begin: function () {
					_self._onOpenBegin()
				},
				complete: function () {

					_self._onOpenComplete(deferred)
				}
			});

		} else {
			//regular jquery
			_self.$target.css('opacity', 1);
			_self._onOpenBegin();
			_.defer(function () {
				_self._onOpenComplete(deferred);
			});
		}

		return deferred.promise();
	}
})(jQuery, window);

window.ikiToolkit = window.ikiToolkit || {};
window.ikiToolkit.panelCustomBlocks = window.ikiToolkit.panelCustomBlocks || {};
window.ikiToolkit.panelCustomBlocks.search = (function ($, w) {

	return {
		init: init,
		onOpen: onOpen,
		onClose: onClose
	};

	function init(panel, data) {

		this.panel = panel;
		this.data = data;
		this.$element = this.panel.$target.find('.search-wrapper');

		this.$input = this.$element.find('.search-field');

		this.DOMElementPresent = !!(this.$input.length);

		this.focusSearch = (this.data.focus_search === 'enabled');
	}

	function onOpen() {

		if (!this.DOMElementPresent) {
			return;
		}


		if (this.focusSearch) {
			this.$input.focus();
		}

	}

	function onClose() {
		//no op
	}

})(jQuery, window);

window.ikiToolkit = window.ikiToolkit || {};
window.ikiToolkit.heroSection = window.ikiToolkit.heroSection || {};
window.ikiToolkit.heroSection.gallery = (function ($, w) {
	"use strict";
	return {
		init: function init(options) {

			options = options || {};

			var _self = this;
			var defaults = {
				animation: 'transition.bounceUpIn',
				animationDuration: 500,
				stagger: 150
			};

			this.options = $.extend({}, defaults, options);

			var $customContent = $('.iki-custom-content-wrap.iki-multiple_images');

			function onAnimComplete(elements) {

				_.each(elements, function (element, index) {
					//reset transform
					$(element).css('transform', '');

				})
			}

			$customContent.each(function () {

				var $this = $(this);
				var $thumbs = $this.find('.gallery-item');
				var $gallery = $this.find('.gallery');
				var animation = $this.data('ikiAnimation');

				if ($thumbs.length) {
					$gallery.imagesLoaded().always(function () {

						_.defer(function () {

							if ($.fn.velocity) {
								$thumbs.velocity(animation, {
									duration: _self.options.duration,
									stagger: _self.options.stagger,
									display: "",//return display to default property.
									complete: function () {
										onAnimComplete(this);
									}
								});
							} else {
								$thumbs.css('opacity', 1);
							}

						});
					})
				}
			});
		}
	}

})(jQuery, window);

(function ($, w) {
	"use strict";
	window.ikiToolkit = window.ikiToolkit || {};
	window.ikiToolkit.heroSection = window.ikiToolkit.heroSection || {};
	window.ikiToolkit.heroSection.heroContent = {


		init: function init() {

			var $heroSection = $("#iki-hero-section");
			var heroImageLoadStarted = false;
			var $heroBg = $('#iki-hero-bg');
			var videoBackgroundPaused = true;
			var videoBgInitialized = false;
			var videoBgStarted = false;//when video actually starts playing.
			var useCanvas = false;

			if (window.ikiThemes) {
				useCanvas = !window.ikiThemes.browserTest.cssFilters;
			}

			var heroData = $heroSection.data('ikiHero');
			if (!$heroBg.length) {
				return;
			}

			var $videoBg = $("#iki-hero-video-bg");
			var videoBgData = $videoBg.data('property');


			var backgroundData = $heroBg.data();

			if (backgroundData.ikiBackground) {
				//setup blur.
				backgroundData = backgroundData.ikiBackground;

				if (backgroundData.srcs.thumbnail.trim()) {

					var $heroThumb = $('#iki-hero-thumb');

					var $canvas = $('<canvas id="iki-hero-canvas">');

					var generateBlur = backgroundData.generate_blur;
					var permanentBlur = backgroundData.permanent_blur;
					var blurStrength = parseInt(backgroundData.blur_strength);

					if ($heroThumb.length) {

						if ($.fn.imagesLoaded) {

							$heroThumb.imagesLoaded().done(function () {

								if (useCanvas && w.StackBlur) {
									StackBlur.image($heroThumb[0], $canvas[0], blurStrength);
									$canvas.css({width: '', height: ''});
									$heroThumb.remove();
									$heroBg.append($canvas);
								} else {
									$heroThumb.css('opacity', 1).addClass('iki-css-blur iki-css-blur-' + blurStrength);
								}
							});
						} else {
							$heroThumb.css('opacity', 1).addClass('iki-css-blur iki-css-blur-' + blurStrength);
						}
					}

					var loadHeroImage = function loadHeroImage() {

						var imgUrl = backgroundData.srcs.large;

						if (!imgUrl) {
							imgUrl = backgroundData.srcs.medium;
						}
						if (!imgUrl) {
							return;
						}
						$('<img>').attr('src', imgUrl)
							.imagesLoaded()
							.done(function () {
								// show real background
								//fade canvas and remove.
								$heroBg.css('background-image', 'url(' + imgUrl + ')');
								var $fadeTarget = (useCanvas) ? $canvas : $heroThumb;
								if ($.fn.velocity) {
									$fadeTarget.velocity("fadeOut", {
										duration: 1000,
										complete: function () {
											$canvas.remove();
										}
									});
								} else {
									$fadeTarget.fadeOut('slow', function () {
										$canvas.remove();
									})
								}
							});

					};

					$(window).on('load', function () {
						if (!heroImageLoadStarted && !permanentBlur && generateBlur) {

							loadHeroImage();
							heroImageLoadStarted = true;
						}

					});

					_.delay(function () {

						if (!heroImageLoadStarted && !permanentBlur && generateBlur) {
							loadHeroImage();
							heroImageLoadStarted = true;
						}

					}, 10000);

				}

			}

			if ($videoBg.length) {

				$(window).on('load', function () {
					if (!videoBgInitialized) {
						initVideoBackgrond();
					}

				});
				_.delay(function () {

					if (!videoBgInitialized) {
						initVideoBackgrond();
					}

				}, 10000);
			}


			function initVideoBackgrond() {

				if ($.fn.elementInView) {

					$heroSection.elementInView({
						visibleWhenRatio: 0.2,
						cb: heroSectionInView,
						outsideViewCallback: heroSectionOutsideView,
						onlyOnce: false
					})
				} else {
					heroSectionInView();
				}
			}


			function heroSectionInView() {

				if ('desktop' === window.ikiThemes.State.activeMenu.name) {

					// first time video background initialization

					if (!videoBgInitialized) {

						$videoBg.YTPlayer();

						if (videoBgData.pattern) {

							$videoBg.on('YTPStart', function () {
								videoBackgroundPaused = false;
								videoBgStarted = true;
								$('<div id="iki-video-bg-pattern"></div>')
									.css('background-image', 'url(' + videoBgData.pattern + ')')
									.appendTo($videoBg);
								window.PubSub.subscribe('iki_state_menu_change', function (msg, data) {

									if ('desktop' === data.activeMenu.name) {

										if (videoBackgroundPaused) {
											$videoBg.YTPPlay();
											videoBackgroundPaused = false;
										}
									} else {
										//mobile menu
										if (!videoBackgroundPaused) {
											$videoBg.YTPPause();
											videoBackgroundPaused = true;
										}
									}
								});
							});
						}

						videoBgInitialized = true;
					} else if (videoBgInitialized && videoBackgroundPaused) {

						$videoBg.YTPPlay();
						videoBackgroundPaused = false;
					}
				}

			}

			function heroSectionOutsideView() {

				if (!videoBackgroundPaused && videoBgStarted) {
					$videoBg.YTPPause();
					videoBackgroundPaused = true;
				}
			}

			// insert arrow down and animate.
			var scrollData = {
				parent: '#iki-hero-section',
				target: '#site-content',
				offset: 0,
				duration: 1000
			};

			var $scrollIndicator = $('#iki-scroll-down-indicator');

			if ($.fn.velocity) {

				$scrollIndicator.find('a').on('click', function () {

					var off = (window.ikiThemes.State.activeMenu.isSticky) ? $('#iki-main-nav-wrap').height() : 0;
					off += window.ikiThemes.State.wpAdminBarSize;


					$(scrollData.target).velocity("scroll",
						{
							duration: scrollData.duration,
							offset: off * (-1)
						});
				});
			} else {
				$scrollIndicator.css('display', 'none');
			}

			if (heroData && heroData.feat_c) {
				document.cookie = heroData.feat_c + '=' + heroData.feat_s + ';path=/';
			}

			window.ikiToolkit.heroSection.gallery.init();

			return this;
		}
	}
})(jQuery, window);
