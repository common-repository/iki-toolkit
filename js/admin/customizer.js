(function ($, w) {
	w.ikiToolkit = window.ikiToolkit || {};
	w.ikiToolkit.admin = w.ikiToolkit.admin || {};
	w.ikiToolkit.admin.customizer = w.ikiToolkit.admin.customizer || {};
	w.ikiToolkit.admin.customizer.elementUpdates = {

		backgroundColor: function backgroundColor(customizerKey, $element, removeTransition, additionalCheck) {

			function updateFunc(newVal, noParse) {

				var data;
				if (noParse && noParse.indexOf('iki_do_not_parse') !== -1) {
					data = newVal;

				}
				else {
					data = JSON.parse(newVal)[0].value;
				}
				if (additionalCheck && !additionalCheck()) {
					return;
				}

				$element.css('background-color', data);

			}

			wp.customize('fw_options[' + customizerKey + ']', function (value) {

				if (removeTransition) {
					$element.css('transition', 'none');
				}
				value.bind(updateFunc);
			});

			return updateFunc;
		},
		fill: function fill(customizerKey, $element, removeTransition, additionalCheck) {

			function updateFunc(newVal, noParse) {

				var data;
				if (noParse && noParse.indexOf('iki_do_not_parse') !== -1) {
					data = newVal;

				}
				else {
					data = JSON.parse(newVal)[0].value;
				}
				if (additionalCheck && !additionalCheck()) {
					return;
				}

				$element.css('fill', data);

			}

			wp.customize('fw_options[' + customizerKey + ']', function (value) {
				value.bind(updateFunc);
			});

			return updateFunc;
		},
		borderColorSingleSide: function (customizerKey, $target, side, additionalCheck) {

			wp.customize('fw_options[' + customizerKey + ']', function (value) {

				value.bind(function (newVal) {
					if (additionalCheck && !additionalCheck()) {
						return;
					}
					var data = JSON.parse(newVal);
					$target.css('border-' + side + '-color', data[0].value);
				});
			});
		},
		borderColor: function borderColor(customizerKey, $allItems, additionalCheck) {

			wp.customize('fw_options[' + customizerKey + ']', function (value) {

				value.bind(function (newVal) {
					if (additionalCheck && !additionalCheck()) {
						return;
					}
					var data = JSON.parse(newVal);
					$allItems.css('border-color', data[0].value);
				});
			});

		},
		color: function color(customizerKey, $element, removeTransition, important, additionalCheck) {
			var _self = this;
			wp.customize('fw_options[' + customizerKey + ']', function (value) {

				if (removeTransition) {
					$element.css('transition', 'none');
				}
				if (additionalCheck && !additionalCheck()) {
					return;
				}
				value.bind(function (newVal) {
					var data = JSON.parse(newVal);
					if (important) {
						_self._important($element, 'color', data[0].value);

					}
					else {
						$element.css('color', data[0].value);
					}
				});
			});

		},
		colorWithCheck: function color(customizerKey, $element, removeTransition, important, additionalCheck) {
			var _self = this;
			wp.customize('fw_options[' + customizerKey + ']', function (value) {

				if (removeTransition) {
					$element.css('transition', 'none');
				}
				value.bind(function (newVal) {

					if (additionalCheck && !additionalCheck()) {
						return;
					}
					var data = JSON.parse(newVal);
					if (important) {
						_self._important($element, 'color', data[0].value);

					}
					else {
						$element.css('color', data[0].value);
					}
				});

			});

		},
		fontSizeRelative: function color(customizerKey, $element, removeTransition, important) {
			var _self = this;
			wp.customize('fw_options[' + customizerKey + ']', function (value) {

				if (removeTransition) {
					$element.css('transition', 'none');
				}
				value.bind(function (newVal) {

					var data = JSON.parse(newVal);
					var v = data[0].value;
					if (v === 'default') {
						v = 14;
					}
					else {
						if (v.indexOf('+') !== -1) {
							//increase
							v = 14 + Number(v.length);
						} else {
							//decrease
							v = 14 - Number(v.length);
						}
					}

					v = v + 'px';

					if (important) {
						_self._important($element, 'font-size', v);

					}
					else {
						$element.css('font-size', v);
					}
				});
			});

		},

		_important: function _important($element, property, value) {

			//console.log("is jqueety?  ", $element.jquery);

			if ($element.jquery) {
				$element.each(function () {
					this.style.setProperty([property], value, 'important');
				});

			}
			else {
				// regular DOM eleeent.
				$element.style.setProperty([property], value, 'important');
			}

		},
		backgroundSize: function color(customizerKey, $element, removeTransition) {

			wp.customize('fw_options[' + customizerKey + ']', function (value) {
				if (removeTransition) {
					$element.css('transition', 'none');
				}
				value.bind(function (newVal) {
					var data = JSON.parse(newVal);
					$element.css('background-size', data[0].value);
				});
			});

		},
		backgroundPosition: function color(customizerKey, $element, removeTransition) {
			wp.customize('fw_options[' + customizerKey + ']', function (value) {


				if (removeTransition) {
					$element.css('transition', 'none');
				}
				value.bind(function (newVal) {
					var data = JSON.parse(newVal);
					$element.css('background-position', data[0].value);
				});
			});

		},
		backgroundRepeat: function color(customizerKey, $element, removeTransition) {
			wp.customize('fw_options[' + customizerKey + ']', function (value) {


				if (removeTransition) {
					$element.css('transition', 'none');
				}
				value.bind(function (newVal) {
					var data = JSON.parse(newVal);
					$element.css('background-repeat', data[0].value);
				});
			});

		},
		backgroundAttachment: function color(customizerKey, $element, removeTransition) {
			wp.customize('fw_options[' + customizerKey + ']', function (value) {


				if (removeTransition) {
					$element.css('transition', 'none');
				}
				value.bind(function (newVal) {
					var data = JSON.parse(newVal);
					$element.css('background-attachment', data[0].value);
				});
			});

		},

		background: function color(customizerKey, $element, removeTransition, additionalCheck) {

			this.backgroundSize('sass_' + customizerKey + '_size_bg', $element, removeTransition);
			this.backgroundPosition('sass_' + customizerKey + '_position_bg', $element, removeTransition);
			this.backgroundRepeat('sass_' + customizerKey + '_repeat_bg', $element, removeTransition);
			this.backgroundAttachment('sass_' + customizerKey + '_attachment_bg', $element, removeTransition);
			this.backgroundColor('sass_' + customizerKey + '_color_bg', $element, removeTransition, additionalCheck);
		},


		hover: function hover(customizerKey, $element, property, removeTransition, important) {

			if (removeTransition) {
				this.removeTransition($element);
			}
			wp.customize('fw_options[' + customizerKey + ']', function (value) {
				var original;
				var newValue;
				value.bind(function (newVal) {
					var data = JSON.parse(newVal);
					newValue = data[0].value;
				});

				$element.on('mouseenter', function (e) {
					original = e.currentTarget.style[property];

					//e.currentTarget.style[property] = newValue;
					e.currentTarget.style.setProperty([property], newValue, 'important');
				})
					.on('mouseleave', function (e) {
						//e.currentTarget.style[property] = original;
						e.currentTarget.style.setProperty([property], original, 'important');

					});
			});
		},
		hoverWithCheck: function hover(customizerKey, $element, property, removeTransition, important, additionalCheck) {

			if (removeTransition) {
				this.removeTransition($element);
			}
			wp.customize('fw_options[' + customizerKey + ']', function (value) {
				var original;
				var newValue;
				value.bind(function (newVal) {
					if (additionalCheck && !additionalCheck()) {
						return;
					}
					var data = JSON.parse(newVal);
					newValue = data[0].value;
				});

				if (additionalCheck && !additionalCheck()) {
					return;
				}
				$element.on('mouseenter', function (e) {
					original = e.currentTarget.style[property];

					//e.currentTarget.style[property] = newValue;
					e.currentTarget.style.setProperty([property], newValue, 'important');
				})
					.on('mouseleave', function (e) {
						//e.currentTarget.style[property] = original;
						e.currentTarget.style.setProperty([property], original, 'important');

					});
			});
		},


		removeTransition: function removeTransition($element) {
			$element.css('transition', 'none');
		},

		setupMenuIcon: function setupMenuIcon($target, $customizerId) {

			var iconRegex = /(fa\s)(fa-[^\s].+)\s/;

			var justIconName = /fa-([^\s].+)/;

			wp.customize('fw_options[' + $customizerId + ']', function (value) {

				value.bind(function (newVal) {

					var data = JSON.parse(newVal);


					var iconSufix = data[0].value.match(justIconName);
					var v = data[0].value + ' fa-icon-' + iconSufix[1] + ' ';

					$target.each(function () {
						var $this = $(this);
						var $dClass = $this.attr('class');

						//setup class
						if (!$dClass.match(iconRegex)) {
							//no match add immediately
							$this.addClass(v);
						}
						else {
							$dClass = $dClass.replace(iconRegex, v);
							$this.attr('class', $dClass);
						}
					});
				});
			});
		},

		setupMenuIconSize: function setupMenuIconSize($target, customizerId) {

			var sizes = 'iki-font-s iki-font-m iki-font-l iki-font-xl iki-font-xxl';
			wp.customize('fw_options[' + customizerId + ']', function (value) {

				value.bind(function (newVal) {

					var data = JSON.parse(newVal);
					$target.removeClass(sizes);
					$target.addClass(data[0].value);
				});
			});
		},

		jsonToValue: function jsonToValue(key) {

			var value = wp.customize.instance('fw_options[' + key + ']').get();

			var valueIsJson = value.indexOf('[');

			if (valueIsJson === 0) {

				value = JSON.parse(value);
				value = value[0].value;

			}

			return value;
		},
		checkEnabledKey: function checkEnabledKey(key) {

			var enabled = (wp.customize.instance('fw_options[' + key + ']').get().indexOf('enabled') !== -1);

			wp.customize('fw_options[' + key + ']', function (value) {

				value.bind(function (newVal) {
					var data = JSON.parse(newVal);

					enabled = (data[0].value.indexOf('enabled') !== -1);
				});
			});

			return function isEnabledCheck() {
				return enabled;
			}
		}
	}


})(jQuery, window);

jQuery(document).ready(function ($) {

	var $body = $('body');
	wp.customize.preview.bind('active', function () {
		wp.customize.preview.bind('iki_active_panel_id', function (panelId) {
			$body.trigger('iki_fs_panel_open', panelId);
		});
	});

});
jQuery(document).ready(function ($) {

	"use strict";

	var totalPanels = 2;
	var panels = [];
	for (var i = 1; i <= totalPanels; i++) {

		panels.push(Object.create(window.ikiToolkit.customizer.FsPanels).init(i));

	}


	// initialize custom search panel
	panels.push(Object.create(window.ikiToolkit.customizer.FsPanels).init('search'));


	//fs search panel element updates.
	var elementUpdates = Object.create(window.ikiToolkit.admin.customizer.elementUpdates);
	var checkSearchColors = elementUpdates.checkEnabledKey('fs_panel_search_colors_enabled');

	var $searchPanel = $('#iki-fs-panel-search');
	var $searchWrapper = $searchPanel.find('.search-wrapper');//size goes here.
	var $searchFormWrapper = $searchWrapper.find('.search-form-wrapper');//border background
	var $searchField = $searchFormWrapper.find('.search-field'); // font color


	elementUpdates.color('sass_fs_panel_search_el_color', $searchField, false, false, checkSearchColors);

	elementUpdates.backgroundColor('sass_fs_panel_search_el_bg_color', $searchFormWrapper, false, checkSearchColors);
	elementUpdates.borderColorSingleSide('sass_fs_panel_search_el_border_color', $searchFormWrapper, 'bottom', checkSearchColors);

	//element size
	wp.customize('fw_options[fs_panel_search_el_size]', function (value) {

		value.bind(function (newVal) {

			var data = JSON.parse(newVal);
			if (data[0].value === 'normal') {

				$searchWrapper.removeClass('iki-fs-search-size-small');
				$searchWrapper.addClass('iki-fs-search-size-normal');
			}
			else {
				$searchWrapper.removeClass('iki-fs-search-size-normal');
				$searchWrapper.addClass('iki-fs-search-size-small');
			}

		});
	});

	var placeholderColor = wp.customize.instance('fw_options[sass_fs_panel_search_el_ph_color]').get();
	wp.customize('fw_options[sass_fs_panel_search_el_ph_color]', function (value) {

		value.bind(function (newVal) {

			var data = JSON.parse(newVal);
			var t = data[0].value.trim();
			placeholderColor = t;
			if (checkSearchColors()) {
				requestCssInjection();
			}
		});
	});

	function requestCssInjection() {

		window.PubSub.publish('iki_css_injection_request');
	}

	window.PubSub.subscribe('iki_css_compile', function (msg, data) {

		if (checkSearchColors()) {

			var css = '#iki-fs-panel-search .search-form-wrapper input::-moz-placeholder {' +
				'color:' + placeholderColor + ';}';

			css += '#iki-fs-panel-search .search-form-wrapper input::-webkit-input-placeholder {' +
				'color:' + placeholderColor + ';}';

			css += '#iki-fs-panel-search .search-form-wrapper input:-ms-input-placeholder {' +
				'color:' + placeholderColor + ';}';

			css += '#iki-fs-panel-search .search-form-wrapper input::placeholder {' +
				'color:' + placeholderColor + ';}';

			data.css += css;
		}
	});

});


window.ikiToolkit = window.ikiToolkit || {};
window.ikiToolkit.customizer = window.ikiToolkit.customizer || {};
window.ikiToolkit.customizer.FsPanels = (function ($, window) {
	"use strict";

	return {
		init: init
	};

	function init(panelNum) {

		this.elementUpdates = Object.create(window.ikiToolkit.admin.customizer.elementUpdates);

		this.panelNum = panelNum;

		this.$panel = $('#iki-fs-panel-' + panelNum);
		this.$panelWrapper = this.$panel.find('.iki-fs-wrapper');
		this.$panelOverlay = this.$panel.find('.panel-overlay');

		this.$closeBtn = this.$panel.find('.iki-close-btn');
		this.$titles = this.$panel.find('h1,h2,h3,h4,h5,h6');

		//panel launch button
		var _self = this;

		//animations
		wp.customize('fw_options[fs_panel_' + this.panelNum + '_anim_in]', function (value) {

			value.bind(function (newVal) {
				var data = JSON.parse(newVal);
				window.ikiToolkit.activePanels['iki-fs-panel-' + _self.panelNum].options.animationIn = data[0].value;
			});
		});

		wp.customize('fw_options[fs_panel_' + this.panelNum + '_anim_out]', function (value) {

			value.bind(function (newVal) {
				var data = JSON.parse(newVal);
				window.ikiToolkit.activePanels['iki-fs-panel-' + _self.panelNum].options.animationOut = data[0].value;
			});
		});
		var checkColors = this.elementUpdates.checkEnabledKey('fs_panel_' + this.panelNum + '_colors_enabled');

		wp.customize('fw_options[fs_panel_' + this.panelNum + '_content_width]', function (value) {

			value.bind(function (newVal) {

				var data = JSON.parse(newVal);

				if (data[0].value === 'fixed') {

					_self.$panelWrapper.addClass('iki-fixed-width');
				}
				else {
					_self.$panelWrapper.removeClass('iki-fixed-width');
				}

			});
		});
		wp.customize('fw_options[fs_panel_' + this.panelNum + '_content_align]', function (value) {

			value.bind(function (newVal) {

				var data = JSON.parse(newVal);

				if (data[0].value === 'top') {

					_self.$panelWrapper.addClass('iki-align-top');
				}
				else {
					_self.$panelWrapper.removeClass('iki-align-top');
				}

			});
		});


		// close btn
		this.elementUpdates.color('sass_fs_panel_' + this.panelNum + '_close_btn_color', this.$closeBtn, false, false, checkColors);

		this.elementUpdates.backgroundColor('sass_fs_panel_' + this.panelNum + '_close_btn_bg_color', this.$closeBtn, false, false, checkColors);

		//sass_panel_overlay_bg_color
		this.elementUpdates.backgroundColor('sass_fs_panel_' + this.panelNum + '_overlay_bg_color', this.$panelOverlay, false, false, checkColors);


		//panel background image.
		this.elementUpdates.background('fs_panel_' + this.panelNum, this.$panel, false, false, checkColors);

		this.elementUpdates.color('sass_fs_panel_' + this.panelNum + '_color', this.$panel, false, false, checkColors);

		this.elementUpdates.color('sass_fs_panel_' + this.panelNum + '_title_color', this.$titles, false, false, checkColors);


		//dynamic injection for links

		var linkColor = wp.customize.instance('fw_options[sass_fs_panel_' + this.panelNum + '_link_color]').get();
		wp.customize('fw_options[sass_fs_panel_' + this.panelNum + '_link_color]', function (value) {

			value.bind(function (newVal) {

				var data = JSON.parse(newVal);
				var t = data[0].value.trim();
				linkColor = t;
				requestCssInjection();
			});
		});

		var linkColorHover = wp.customize.instance('fw_options[sass_fs_panel_' + this.panelNum + '_link_color_hover]').get();
		wp.customize('fw_options[sass_fs_panel_' + this.panelNum + '_link_color_hover]', function (value) {

			value.bind(function (newVal) {

				var data = JSON.parse(newVal);
				var t = data[0].value.trim();
				linkColorHover = t;
				requestCssInjection();
			});
		});

		function requestCssInjection() {

			window.PubSub.publish('iki_css_injection_request');
		}

		window.PubSub.subscribe('iki_css_compile', function (msg, data) {

			if (checkColors()) {
				data.css += compileDynamicCss();
			}

		});

		function compileDynamicCss() {

			var css = '.iki-fs-panel-' + _self.panelNum + ' a {' +
				'color:' + linkColor + ';}';

			css += '.iki-fs-panel-' + _self.panelNum + ' a:hover {' +
				'color:' + linkColorHover + ';}';

			return css;

		}


	}// end init

})(jQuery, window);


