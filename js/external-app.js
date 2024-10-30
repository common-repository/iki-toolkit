window.ikiToolkit = window.ikiToolkit || {};
window.ikiToolkit.external = window.ikiToolkit.external || {};
window.ikiToolkit.external.externalService = (function ($, w) {

	"use strict";

	var page = {};

	page.init = function init(data, app, api, demoApp) {
		this.data = data;
		this.app = app;
		this.api = api;
		this.api.init({token: data.options.external.token, adminUrl: window.ikiThemeExports.adminUrl});

		this.userProfile = false;
		if (data.options.external.userProfile) {
			this.userProfile = data.options.external.userProfile;
		}

		this.userImages = false;
		if (data.options.external.userImages) {

			this.userImages = data.options.external.userImages;
		}

		var thumbTemplate = $('#iki-thumb-template').html();
		var compiledThumbTemplate = _.template(thumbTemplate);

		this.appData = $.extend({}, data.options.external,
			{
				userProfile: this.userProfile,
				userImages: this.userImages,
				startPage: 2,//when not in demo first page is embeded. so first actual requeset goes to 2
				imagesContainer: $('.iki-external-images'),
				loadMoreBtn: $('.iki-progress-btn'),
				api: api,
				thumbTemplate: compiledThumbTemplate,
				thumbAnim: data.thumbAnim,// ovo moram resiti.
				thumbAnimStagger: 180,
				$menu: $('#iki-main-nav-wrap'),
				lightboxData: w.ikiThemeExports.theme.options.lightbox.data,
				fullWidth: false
			});

		this.initializeApp();
	}; //end init


	page.initializeApp = function checkHaveData() {

		this.app.init(this.appData);
	};
	return page;

}(jQuery, window));

window.ikiToolkit = window.ikiToolkit || {};
window.ikiToolkit.external = window.ikiToolkit.external || {};
window.ikiToolkit.external.AbstractApp = (function ($, window) {

	"use strict";

	return {
		init: init,
		buildNewImages: buildNewImages,
		theEnd: theEnd,
		populateUserProfile: populateUserProfile,
		buildUserProfileData: buildUserProfileData,
		loadMore: loadMore,
		animateImages: animateImages,
		getAssets: getAssets,
		assetsLoadSuccess: assetsLoadSuccess,
		assetsLoadFailure: assetsLoadFailure,
		imagesBuildDone: imagesBuildDone,
		checkIfEnd: checkIfEnd,
		processNextImageData: processNextImageData,
		disableUi: disableUi,
		enableUi: enableUi,
		getLightBoxTargets: getLightBoxTargets,
		updateLightBox: updateLightBox,
		getNextIndex: getNextIndex,
		_initLightbox: initLightbox,
		_trimMaxImages: _trimMaxImages,
		_checkExportDataError: _checkExportDataError,
		_alertDataError: _alertDataError,
		_createExternalLink: _createExternalLink
	};


	function init(data) {

		var defaults = {
			thumbAnim: 'transition.perspectiveUpIn',
			thumbAnimStagger: 100,
			scrollAnimDuration: 700
		};

		this.data = $.extend({}, defaults, data);
		var isAdminRegex = /administrator|author|editor/;
		this.showAlerts = isAdminRegex.test(ikiThemeExports.theme.options.user_roles);
		this.extLinkText = window.ikiThemeExports.module.ext_link_text;//lightbox link text
		this.assetIndex = 1;
		this.totalAssets = 0;
		this.userProfile = data.userProfile;
		this.userImages = data.userImages;
		this.token = data.token;
		this.useLocalConnection = data.useLocalConnection;
		this.userName = data.userName;

		this.currentPage = this.data.startPage;
		this.currentBatch = 0;
		this.pageIterator = Object.create(window.ikiToolkit.external.PageIterator);

		this.api = this.data.api;
		this.pageBuilder = Object.create(window.ikiToolkit.external.PageBuilder);

		this.$loadMoreBtn = this.data.loadMoreBtn;

		this.laddaBtn = undefined;

		if (window.ikiThemes.progressBtn) {
			this.laddaBtn = Object.create(window.ikiThemes.progressBtn);
			this.laddaBtn.init(this.$loadMoreBtn);
		}

		this.animator = undefined;
		if (window.ikiThemes.utils.Animator) {
			this.animator = Object.create(window.ikiThemes.utils.Animator);
			this.animator.init({
				animationIn: this.data.thumbAnim,
				stagger: this.data.stagger
			});
		}

		this.$body = $('body');
		this.checkBodyScroll = true;


		this.$placeholders = this.data.imagesContainer.find('.iki-ext-placeholder');
		var userProfileCheck = this._checkExportDataError(this.userProfile);

		if (!userProfileCheck.error) {
			if (this.userProfile) {
				// build user profile.
				this.populateUserProfile(this.userProfile.data);
			}
		}

		var imagesDataCheck = this._checkExportDataError(this.userImages);
		if (!imagesDataCheck.error) {
			//build user images
			this.latestData = this.userImages;

			this.pageIterator.init(this.userImages.data, this.data.imagesPerPage);
			this.loadMore(null, true);
			if (this.laddaBtn) {
				this.laddaBtn.setText(this.$loadMoreBtn.data('inProgress'));
			}
		}
		else {
			//if admin
			this._alertDataError(imagesDataCheck);
			return false;
		}

		this.loadMoreBinded = this.loadMore.bind(this);// so i can remove it later.
		this.$loadMoreBtn.on('click', this.loadMoreBinded);

		if (window.ikiThemes) {
			this._initLightbox();
		}

	} // end init

	function _createExternalLink(link, text) {
		return '<strong><a class="iki-lb-ext-link" target="_blank" href="' + link + '">' + text + '</a></strong>';
	}

	function _alertDataError(data) {

		if (data.message && data.message.trim() && this.showAlerts) {
			// alert(data.message);
			$('body').addClass('iki-ext-error');

			// this.$placeholders.remove();
			$('.iki-external-images').find('.iki-ext-placeholder');

			$.magnificPopup.open({
				items: {
					src: '<div class="iki-ext-notification clearfix"><p class="iki-ext-message">' + data.message + '</p></div>',
					type: 'inline'
				},
				callbacks: {
					open: function () {
						$('.mfp-close').insertBefore('.iki-ext-message');
					}
				}
			});
		}
		if (this.laddaBtn) {
			this.laddaBtn.setText(this.$loadMoreBtn.data('error'));
			this.laddaBtn.stop();
		}
	}


	function _checkExportDataError() {
		return {
			error: false,
			message: '',
			status: ''
		}
	}

	function buildNewImages(imageData) {

		var deferred = new $.Deferred();
		this.currentBatch++;

		//hook to preprocess image data (because of flickr image construction)
		imageData = this.processNextImageData(imageData);

		var _self = this;
		var total = imageData.length;
		var loaded = 0;


		_self._trimMaxImages(imageData, _self.totalAssets, _self.data.maxTotal);

		_self.totalAssets += total;

		//trim image data
		// don't make $imageBatchWrapper single instance (on class level) because of potentially different designs
		var $imageBatchWrapper = $('<div></div>')
			.css('display', 'none')
			.attr('id', 'iki-batch-wrapper' - +this.currentBatch);//.appendTo(this.$body);

		var imageBatch = $(this.pageBuilder.buildUserImages(imageData, this.data.thumbTemplate));

		$imageBatchWrapper.append(imageBatch);

		_.defer(function () {
			$imageBatchWrapper.imagesLoaded().always(function (instance) {

				if (_self.laddaBtn) {
					_self.laddaBtn.stop();
					_self.laddaBtn.setText(_self.laddaBtn.originalText);
				}

				_self.data.imagesContainer.append(imageBatch);

				$imageBatchWrapper.remove();
				_self.$placeholders.remove();
				var $animTargets = imageBatch.filter('.iki-thumb-container');

				$animTargets.find('.tooltip-js')
					.tooltipster($.extend({}, window.ikiToolkit.defaultTooltipOptions, {position: 'top'}));

				$animTargets.find('.close-tt-js').on('click', function (e) {
					e.preventDefault();
					$(this).tooltipster('close');
				});

				deferred.resolve($animTargets, $($animTargets.get(0)));
			}).progress(function (instance, image) {

				loaded++;
				if (_self.laddaBtn) {
					_self.laddaBtn.setProgress((loaded / total) * 100);
				}
			});
		});

		$('body').append($imageBatchWrapper);

		return deferred.promise();
	}

	function theEnd() {
		if (this.laddaBtn) {
			this.laddaBtn.disable();
			this.laddaBtn.setText(this.$loadMoreBtn.attr('data-all-loaded'));
		}
	}

	function disableUi() {
		this.$loadMoreBtn.attr({disabled: true});
	}

	function enableUi() {
		this.$loadMoreBtn.attr({disabled: false});

	}

	function populateUserProfile(data) {

		var constructedData = this.buildUserProfileData(data);
		if (ikiThemeExports.module.show_profile) {
			this.pageBuilder.buildUserProfile(constructedData);
			window.PubSub.publish('iki_profile_populated', constructedData);
		}
	}

	function buildUserProfileData(data) {
		//noop abstract.
		throw new Error('buildUserProfileData method is abstract');

	}

	function loadMore(e, disableAnimation) {

		var _self = this;

		if (e) {
			e.preventDefault();
		}

		disableAnimation = disableAnimation || false;
		if (this.laddaBtn) {
			_self.laddaBtn.start();
			_self.laddaBtn.setText(this.$loadMoreBtn.data('inProgress'));
		}


		if (_self.pageIterator.hasNext()) {

			_self.buildNewImages(_self.pageIterator.next())
				.done(function ($animationTargets, $scrollTarget) {
					_self.imagesBuildDone($animationTargets, $scrollTarget, disableAnimation);
				})
		}
		else {
			_self.getAssets()
				.done(_self.assetsLoadSuccess.bind(_self))
				.fail(_self.assetsLoadFailure.bind(_self));
		}

		// else go get the new batch over the wire
	}

	function animateImages($animationTargets, $scrollTarget, disableScroll) {

		var _self = this;
		var defer = $.Deferred();

		disableScroll = disableScroll || false;
		_self.disableUi();

		// find all images and animate them
		// scroll the window
		var offset = parseInt($scrollTarget.css('margin-left'), 10);
		if (window.ikiThemes) {
			offset += +window.ikiThemes.State.wpAdminBarSize;
			if (window.ikiThemes.State.activeMenu.isSticky) {

				var bodyScroll = _self.$body.scrollTop();
				var hideAt = parseInt(window.ikiThemeExports.theme.layout.header.hide_at);
				if (isNaN(hideAt)) {
					hideAt = 10000000;
				}

				if (_self.checkBodyScroll && hideAt && (bodyScroll < hideAt)) {
					offset += (window.ikiThemes.State.activeMenu.isSticky) ? _self.data.$menu.height() : 0;
				}
				else {
					//small performance optimizactin
					_self.checkBodyScroll = false;
				}
			}
		}

		if (disableScroll) {
			if (_self.animator) {
				_self.animator.animate($animationTargets, null, 'inline-block')
					.done(function () {
						defer.resolve();
					})
			} else {
				$animationTargets.css('opacity', 1);
				defer.resolve();
			}
		}
		else {
			_.defer(function () {

				if ($.fn.velocity) {

					$scrollTarget.velocity('scroll',
						{
							offset: (offset * -1) + 'px',
							duration: _self.data.scrollAnimDuration,
							complete: function () {
								if (_self.animator) {
									_self.animator.animate($animationTargets, null, 'inline-block').done(function () {
										defer.resolve();
									})
								} else {
									defer.resolve();
								}
							}
						})
				} else {
					defer.resolve();
				}
			})
		}


		return defer.promise();
	}

	function getAssets() {
		//no opp
		throw new Error('This method needs to be extended (abstract)');
	}

	function _trimMaxImages(newData, currentTotal, maxTotal) {

		if (newData.length + currentTotal > maxTotal) {

			newData.splice(maxTotal - currentTotal, newData.length);
		}
	}

	function assetsLoadSuccess(response, status, xhr) {

		var _self = this;

		_self.currentPage++;// increment for the next page.
		_self.latestData = response;

		_self._trimMaxImages(_self.latestData.data, _self.totalAssets, _self.data.maxTotal);

		_self.pageIterator.setNewData(_self.latestData.data, _self.data.imagesPerPage);

		return this.buildNewImages(_self.pageIterator.next())
			.done(function ($animationTargets, $scrollTarget) {

				_self.disableUi();
				var theEnd = _self.data.disablePaging || _self.totalAssets >= _self.data.maxTotal;

				_self.animateImages($animationTargets, $scrollTarget).done(function () {

					if (_self.data.disablePaging || _self.checkIfEnd() || theEnd) {
						_self.theEnd();
					}
					else {
						_self.enableUi();
					}

				});

			});
	}

	function assetsLoadFailure(response, status, xhr) {

		this._alertDataError(this._checkExportDataError(response));

		if (this.laddaBtn) {
			this.laddaBtn.setText(this.$loadMoreBtn.data('error'));
			this.laddaBtn.stop();
		}
	}

	function imagesBuildDone($animationTargets, $scrollTarget, disableAnimation) {

		var _self = this;
		this.disableUi();

		if (!this.firstBatch) {
			this.firstBatch = true;
		}

		var theEnd = _self.data.disablePaging || this.totalAssets >= this.data.maxTotal;

		this.animateImages($animationTargets, $scrollTarget, disableAnimation).done(function () {

			if (theEnd || _self.checkIfEnd()) {
				_self.theEnd();
			}
			else {
				_self.enableUi();
			}
		});
	}

	function checkIfEnd() {

		return !this.pageIterator.hasNext();//flip to return true when we are at the end
	}

	function processNextImageData(imageData) {
		// some kind of logic needs to be in children (if needed)
		var mfp = $.magnificPopup.instance;
		if (this.lightBox && this.lightBox.isOpen) {
			mfp.updateItemHTML();
		}
		return imageData;
	}

	function getLightBoxTargets() {
		return $('.iki-external-images');
	}

	function updateLightBox(src) {
		var mfp = $.magnificPopup.instance;

		if (this.lightBox && this.lightBox.isOpen) {
			mfp.items.push({
				src: src
			});
		}
	}

	function getNextIndex() {

		return this.assetIndex++;
	}

	function initLightbox() {
		this.lightBox = Object.create(window.ikiThemes.Lightbox);
		this.lightBox.init({
			lightbox: {
				$targets: this.getLightBoxTargets(),
				animations: this.data.lightboxData.animations,
				openCloseAnimation: this.data.lightboxData.openCloseAnimation
			},
			mfp: {
				//fixedContentPos: false,
				image: {
					titleSrc: function (item) {

						var text = item.el.parent().siblings('.iki-desc').html();

						if (!text || !text.trim().length) {
							text = item.el.parent().parent().siblings('.iki-thumb-title').text();
						}

						return text;
					}
				}
			}
		});
	}

})(jQuery, window);

window.ikiToolkit = window.ikiToolkit || {};
window.ikiToolkit.external = window.ikiToolkit.external || {};
window.ikiToolkit.external.flickr = (function ($, window) {

	"use strict";

	var page = Object.create(window.ikiToolkit.external.externalService);

	page.init = function init(data) {

		var app = Object.create(window.ikiToolkit.external.flickr.Main);

		var api = Object.create(window.ikiToolkit.external.flickr.Api);

		var demoApp = null;
		window.ikiToolkit.external.externalService.init.call(this, data, app, api, demoApp);

	};

	page.initializeApp = function initializeApp() {

		if (this.userImages && this.userImages.stat === 'ok') {
			//remap , to support abstract app data
			if (!this.data.options.external.showStream) {
				//looking at photoset , need to remap
				this.api.remapPhotosetPhotoData(this.userImages, true);

			}
			else {
				this.api.remapStreamPhotoData(this.userImages, true);
			}

			if (this.userProfile && this.userProfile.stat === 'ok') {
				this.api.remapUserData(this.userProfile, true);
			}

		}
		window.ikiToolkit.external.externalService.initializeApp.call(this);

	};

	return page;

}(jQuery, window));


window.ikiToolkit = window.ikiToolkit || {};
window.ikiToolkit.external = window.ikiToolkit.external || {};
window.ikiToolkit.external = window.ikiToolkit.external || {}
window.ikiToolkit.external.PageBuilder = (function ($, window) {

	return {
		buildUserProfile: buildUserProfile,
		buildUserImages: buildUserImages
	};


	function buildUserProfile(data) {

		var $imageParent = $('.iki-ext-profile');

		var $profileLink = $('<a></a>').attr({
			href: data.home_url,
			target: '_blank',
			class: 'iki-ext-profile-link'
		});

		var $profileImage = $([]);

		if (data.imgSrc) {
			$profileImage = $('<img/>').attr({src: data.imgSrc});

			if (data.home_url) {

				$profileLink.append($profileImage);
				$imageParent.append($profileLink);
			} else {
				$imageParent.append($profileImage);

			}

			delete data.imgSrc;

		} else {

			$profileImage = $imageParent.find('img');

			if (data.home_url && $profileImage.length) {

				$profileLink.append($profileImage);
				$imageParent.append($profileLink);

			}
		}
		if ($profileImage.length && $profileLink.length) {

			var linkTitle = $profileLink.attr('href');

			$profileImage.attr('title', linkTitle)
				.tooltipster(window.ikiToolkit.defaultTooltipOptions);
		}

		$imageParent.imagesLoaded().done(function () {
			_.defer(function () {

				$imageParent.velocity('transition.slideUpIn', {duration: 500});
				$(window).trigger('iki_external_profile_image_loaded');

			});
		});


		delete data.home_url;

		_.each(data, function (value, key) {


			var d = 'span[data-iki-' + key + ']';
			$(d).html(value).parent().css('display', 'inline-block');

		});

	}


	function buildUserImages(data, template) {

		var s = '';
		_.each(data, function (element) {

			s += template(element);
		});

		return $.parseHTML(s);
	}


})(jQuery, window);

window.ikiToolkit = window.ikiToolkit || {};
window.ikiToolkit.external = window.ikiToolkit.external || {};
window.ikiToolkit.external = window.ikiToolkit.external || {}
window.ikiToolkit.external.PageIterator = (function ($, window) {

	return {
		init: init,
		hasNext: hasNext,
		next: next,
		reset: reset,
		setNewData: setNewData
	};


	function init(data, perPage) {

		this.data = data;
		this.index = 0;
		this.perPage = perPage;
	}

	function hasNext() {

		return this.index < this.data.length;
	}

	function next() {

		var r = this.data.slice(this.index, this.index + this.perPage);
		this.index = this.index + this.perPage;
		return r;

	}

	function reset() {
		this.index = 0;
	}

	function setNewData(data, perPage) {

		this.perPage = perPage || this.perPage;
		this.data = data;
		this.reset();
	}
})(jQuery, window);

window.ikiToolkit = window.ikiToolkit || {};
window.ikiToolkit.external = window.ikiToolkit.external || {};
window.ikiToolkit.external.pinterest = (function ($, window) {

	"use strict";

	var page = Object.create(window.ikiToolkit.external.externalService);

	page.init = function init(data) {

		var app = Object.create(window.ikiToolkit.external.pinterest.Main);

		var api = Object.create(window.ikiToolkit.external.pinterest.Api);

		var demoApp = null;
		window.ikiToolkit.external.externalService.init.call(this, data, app, api, demoApp);

	};

	page.initializeApp = function initializeApp() {

		if (this.userImages && this.userImages.channel && this.userImages.channel.item) {
			this.api.remapPhotoData(this.userImages, true);
		}
		window.ikiToolkit.external.externalService.initializeApp.call(this);

	};

	return page;

}(jQuery, window));


window.ikiToolkit = window.ikiToolkit || {};
window.ikiToolkit.external = window.ikiToolkit.external || {};
window.ikiToolkit.external.flickr = window.ikiToolkit.external.flickr || {}
window.ikiToolkit.external.flickr.Api = (function ($, w) {

	"use strict";
	// flickr app - never communicates with wp admin

	return {
		init: init,
		getUser: getUser,
		getUserPhotos: getUserPhotos,
		getPhotosetInfo: getPhotosetInfo,
		getPhotosetPhotos: getPhotosetPhotos,
		findByUsername: findByUsername,
		getProfilePhotoSrc: getProfilePhotoSrc,
		constructUserProfileImgSrc: constructUserProfileImgSrc,
		remapStreamPhotoData: remapStreamPhotoData,
		remapUserData: remapUserData,
		getPhotoSrc: getPhotoSrc,
		remapPhotosetPhotoData: remapPhotosetPhotoData,
		findByUrl: findByUrl,
		buildParams: buildParams,
		getPhotoUrl: getPhotoUrl
	};


	function init(data) {

		/*
		 http://www.flickr.com/services/api/misc.urls.html
		 s	small square 75x75
		 q	large square 150x150
		 t	thumbnail, 100 on longest side
		 m	small, 240 on longest side
		 n	small, 320 on longest side
		 -	medium, 500 on longest side  // illegal character in js
		 z	medium 640, 640 on longest side
		 c	medium 800, 800 on longest side†
		 b	large, 1024 on longest side*
		 o	original image, either a jpg, gif or png, depending on source format*/

		this.imageSizes = {
			s: 's',
			q: 'q',
			t: 't',
			m: 'm',
			n: 'n',
			z: 'z',
			c: 'c',
			b: 'b',
			o: 'o',
			i: '-' // illegal character hack
		};

		this.accessToken = data.token;
		this.adminAjaxUrl = data.adminUrl;

		this.endPoints = {
			'getUser': 'https://api.flickr.com/services/rest?method=flickr.people.getInfo',
			'getUserPhotos': 'https://api.flickr.com/services/rest/?method=flickr.people.getPublicPhotos',
			'getPhotosetInfo': 'https://api.flickr.com/services/rest/?method=flickr.photosets.getInfo',
			'getPhotosetPhotos': 'https://api.flickr.com/services/rest/?method=flickr.photosets.getPhotos',
			'findByUsername': 'https://api.flickr.com/services/rest/?method=flickr.people.findByUsername',
			'lookupUser': 'https://api.flickr.com/services/rest?method=flickr.urls.lookupUser'

		};
	}

	function getUser(userId, params) {

		var r = this.endPoints.getUser;

		r += '&user_id=' + userId;
		var paramsString = this.buildParams(params);
		var promise = sendRequest(r + paramsString);
		var _self = this;

		return promise.done(function (response, status, xhr) {

			response.person.profileImageSrc = _self.getProfilePhotoSrc(response.person);
		});
	}

	function getUserPhotos(userId, params) {

		var r = this.endPoints.getUserPhotos;

		r += '&user_id=' + userId;
		var paramsString = this.buildParams(params);
		return sendRequest(r + paramsString);
	}

	function getPhotosetInfo(params) {

		var r = this.endPoints.getPhotosetInfo;
		var paramsString = this.buildParams(params);
		return sendRequest(r + paramsString);
	}

	function getPhotosetPhotos(params) {
		var r = this.endPoints.getPhotosetPhotos;
		var paramsString = this.buildParams(params);
		return sendRequest(r + paramsString);

	}

	function findByUsername(params) {
		var r = this.endPoints.findByUsername;
		var paramsString = this.buildParams(params);
		return sendRequest(r + paramsString);
	}

	function findByUrl(url, params) {

		if (url.indexOf('flickr.com') < 0) {
			//url is just the username , construct full url.
			url = 'https://www.flickr.com/photos/' + url;
		}
		var r = this.endPoints.lookupUser;
		r += '&url=' + url;
		var paramsString = this.buildParams(params);

		return sendRequest(r + paramsString);
	}

	function buildParams(obj) {

		var s = '&format=json';
		s += '&api_key=' + this.accessToken;
		s += '&jsoncallback=?';

		if (!obj) {
			return s;
		}

		$.each(obj, function (key, value) {
			s += '&' + key + '=' + value;
		});
		return s;
	}

	function sendRequest(request) {

		var deferred = $.Deferred();
		$.ajax({
			type: 'GET',
			url: request,
			dataType: 'jsonp',
			timeout: 10000
		}).done(function (data, status, xhr) {

			if (data === 0 || data.stat === 'fail') {
				status = 'failure';
				deferred.reject(data, status, xhr);
			}
			else {
				deferred.resolve(data, status, xhr);
			}

		}).fail(function (data, status, xhr) {
			data.message = data.message || 'Request Timeout';
			deferred.reject(data, status, xhr);
		});

		return deferred.promise();
	}

	function getProfilePhotoSrc(userData) {

		var profileImageSrc = '';
		if (Number(userData.iconserver) > 0) {
			// we have profile image
			// construct url
			//http://farm{icon-farm}.staticflickr.com/{icon-server}/buddyicons/{nsid}.jpg
			profileImageSrc = constructUserProfileImgSrc(userData);
			//                console.log('we have custom profile image');
		}
		return profileImageSrc;
	}

	function constructUserProfileImgSrc(userData) {
		//size 45x45
		return 'https://farm' + userData.iconfarm + '.staticflickr.com/' + userData.iconserver + '/buddyicons/' + userData.nsid + '.jpg';
	}

	function remapUserData(userData, deleteRemaped) {
		userData.data = userData.person;

		userData.data.username = userData.data.nsid;

		//userData.data.username =
		if (deleteRemaped) {
			delete userData.person;
		}
		return userData;

	}

	function remapStreamPhotoData(photoData, deleteRemaped) {

		photoData.data = photoData.photos.photo;

		if (deleteRemaped) {
			delete photoData.photos.photo;
		}

		return photoData;
	}

	function remapPhotosetPhotoData(photoData, deleteRemaped) {

		photoData.data = photoData.photoset.photo;

		if (deleteRemaped) {
			delete photoData.photoset.photo;
		}

		return photoData;
	}

	function getPhotoSrc(photo, size) {
		return 'https://farm' + photo.farm + '.staticflickr.com/' + photo.server + '/' + photo.id + '_' + photo.secret + '_' + size + '.jpg';
	}

	function getPhotoUrl(photo, userId) {
		//https://www.flickr.com/photos/{user-id}/{photo-id} - individual photo
		return 'https://www.flickr.com/photos/' + userId + '/' + photo.id;
	}

})(jQuery, window);


window.ikiToolkit = window.ikiToolkit || {};
window.ikiToolkit.external = window.ikiToolkit.external || {};
window.ikiToolkit.external.flickr = window.ikiToolkit.external.flickr || {};
window.ikiToolkit.external.flickr.Main = (function ($, w) {

	"use strict";

	var app = Object.create(window.ikiToolkit.external.AbstractApp);
	app.init = init;
	app.assetsLoadSuccess = assetsLoadSuccess;
	app.checkIfEnd = checkIfEnd;
	app.buildUserProfileData = buildUserProfileData;
	app.getAssets = getAssets;
	app.processNextImageData = processNextImageData;
	app._checkExportDataError = _checkExportDataError;

	function init(data) {

		var userProfileCheck = this._checkExportDataError(data.userProfile);
		if (userProfileCheck.error) {

			//hack hacky
			this.$loadMoreBtn = data.loadMoreBtn;
			this.laddaBtn = Object.create(window.ikiToolkit.progressBtn);
			this.laddaBtn.init(this.$loadMoreBtn);
			var isAdminRegex = /administrator|author|editor/;
			this.showAlerts = isAdminRegex.test(ikiThemeExports.theme.options.user_roles);

			this._alertDataError(userProfileCheck);
			return false;
		}
		else {
			window.ikiToolkit.external.AbstractApp.init.call(this, data);
		}


	}

	function _checkExportDataError(data) {

		var r = {
			error: false,
			message: '',
			status: ''
		};

		if (!data) {
			r.error = true;
		}
		else if (data.stat === 'fail') {
			r.error = true;
			r.message = data.message;
			r.status = data.code
		}

		return r;
	}

	function assetsLoadSuccess(response, status, xhr) {

		window.ikiToolkit.external.AbstractApp.assetsLoadSuccess.call(this, response, status, xhr);
	}

	function checkIfEnd() {

		return window.ikiToolkit.external.AbstractApp.checkIfEnd.call(this);
	}

	function buildUserProfileData(data) {

		data.avatar_url = data.profileImagesSrc = this.api.getProfilePhotoSrc(data);
		return {
			imgSrc: (this.data.customProfile) ? false : data.avatar_url,
			'home_url': data.profileurl._content,
			'images-count': data.photos.count._content
		}
	}

	function getAssets() {
		//no op
		var def = $.Deferred();

		setTimeout(function () {
			def.reject();
		}, 100);

		return def.promise();
	}

	function processNextImageData(imageData) {

		var _self = this;

		_.each(imageData, function (value, index) {

			value.ikiThumbSrc = (_self.data.highResolution) ? _self.api.getPhotoSrc(value, _self.api.imageSizes.z) : _self.api.getPhotoSrc(value, _self.api.imageSizes.n);
			value.ikiTitle = value.title;
			value.ikiDescription = '';

			value.ikiThumbId = _self.getNextIndex();
			value.ikiLargeSrc = _self.api.getPhotoSrc(value, _self.api.imageSizes.b);
			value.ikiExtLink = _self.api.getPhotoUrl(value, _self.userProfile.data.nsid);

			if (value.ikiTitle.length) {
				value.ikiDescription = '<span class="iki-lb-title">' + value.ikiTitle + '</span>';
			}

			value.ikiDescription += _self._createExternalLink(value.ikiExtLink, _self.extLinkText);


			_self.updateLightBox(value.ikiLargeSrc);

		});


		window.ikiToolkit.external.AbstractApp.processNextImageData.call(this);
		return imageData;
	}

	return app;

})(jQuery, window);
window.ikiToolkit = window.ikiToolkit || {};
window.ikiToolkit.external = window.ikiToolkit.external || {};
window.ikiToolkit.external.pinterest = window.ikiToolkit.external.pinterest || {}
window.ikiToolkit.external.pinterest.Api = (function ($, w) {

	"use strict";

	return {
		init: init,
		getUserLatestPins: getUserLatestPins,
		// get_user_board: get_user_board,
		remapUserData: remapUserData,
		getPhotoSrc: getPhotoSrc,
		remapPhotoData: remapPhotoData,
		size: {
			S: '/192x/',
			M: '/236x/',
			L: '/550x/',
			XL: '/736x/'
		}
	};


	function init(data) {

		this.accessToken = data.token;
		this.adminAjaxUrl = data.adminUrl;
		this.endPoints = {
			'getUserLatestPins': 'https://pinterest.com/{username}/feed.rss',
			'getUserBoard': 'https://pinterest.com/{username}/{boardname}.rss'
		};
	}


	function remapUserData(userData, deleteRemaped) {
		//no op
	}

	function remapPhotoData(photoData, deleteRemaped) {

		photoData.data = photoData.channel.item;
		if (deleteRemaped) {
			delete photoData.channel.item;
		}

		return photoData;
	}


	function getPhotoSrc(photo, size) {


		var imgSrc = $(photo.description).find('img').attr('src');
		var search = '/192x/';

		if (imgSrc.indexOf(this.size.M) !== -1) {
			search = this.size.M;
		}

		imgSrc = imgSrc.replace(search, size);

		return imgSrc;
	}


	function getUserLatestPins(username) {
		return sendRequest({user: username, method: 'getUserLatestPins'});
	}

	function sendRequest(data) {

		var deferred = $.Deferred();

		data.cache = 'disabled';
		var d = {
			action: 'iki_external_api',
			method: data.method,
			service: 'pinterest',
			data: data,
			_ajax_nonce: w.ikiThemeExports.iki_nonce
		};


		$.ajax({
			type: 'POST',
			url: window.ikiThemeExports.adminUrl,
			dataType: 'json',
			timeout: 10000,
			data: d
		}).done(function (data, status, xhr) {

			if (data === 0 || data.error || data.message) {

				status = 'failure';
				deferred.reject(data, status, xhr);
			}
			else {
				deferred.resolve(data, status, xhr);
			}

		}).fail(function (data, status, xhr) {
			deferred.reject(data, status, xhr);
		});

		return deferred.promise();
	}
})(jQuery, window);


window.ikiToolkit = window.ikiToolkit || {};
window.ikiToolkit.external = window.ikiToolkit.external || {};
window.ikiToolkit.external.pinterest = window.ikiToolkit.external.pinterest || {};
window.ikiToolkit.external.pinterest.Main = (function ($, w) {

	"use strict";

	var app = Object.create(window.ikiToolkit.external.AbstractApp);
	app.init = init;
	app.assetsLoadSuccess = assetsLoadSuccess;
	app.checkIfEnd = checkIfEnd;
	app.buildUserProfileData = buildUserProfileData;
	app.getAssets = getAssets;
	app.processNextImageData = processNextImageData;
	app.theEnd = theEnd;
	//app.buildNewImages = buildNewImages;
	app._checkExportDataError = _checkExportDataError;

	function init(data) {

		if (data.customProfile) {
			data.userProfile = data.userImages;
			this.userProfile = true;
		}

		window.ikiToolkit.external.AbstractApp.init.call(this, data);

	}

	function _checkExportDataError(data) {

		var r = {
			error: false,
			message: '',
			status: ''
		};

		if (!data) {
			r.error = true;
		} else if (data.message) {
			r.error = true;
			r.message = data.message;
		}

		return r;
	}

	function assetsLoadSuccess(response, status, xhr) {

		window.ikiToolkit.external.AbstractApp.assetsLoadSuccess.call(this, response, status, xhr);
	}

	function checkIfEnd() {

		return window.ikiToolkit.external.AbstractApp.checkIfEnd.call(this);
	}

	function theEnd() {
		// don't call parent
		var _self = this;

		this.$loadMoreBtn.removeAttr('disabled');
		if (this.laddaBtn) {
			this.laddaBtn.setText(this.$loadMoreBtn.attr('data-all-loaded'));
		}

		this.$loadMoreBtn.off('click', this.loadMoreBinded);

		this.$loadMoreBtn.on('click', function (e) {

			e.preventDefault();

			window.open(_self.userImages.channel.link, '_blank');
		});

	}

	function buildUserProfileData(data) {

		var homeUrl = '';
		try {
			homeUrl = this.userImages.channel.link;
		} catch (e) {
			// console.log("error no user profile");
		}

		return {
			'home_url': homeUrl
		};
	}

	function getAssets() {
		//no op
	}

	function processNextImageData(imageData) {

		var _self = this;
		var titleRgex = /<\/a>\s*(.+)/;

		_.each(imageData, function (value) {

			var src = _self.api.getPhotoSrc(value, _self.api.size.XL);

			value.ikiThumbSrc = src;
			value.ikiExtLink = value.link;
			value.ikiLargeSrc = src;
			value.ikiThumbId = _self.getNextIndex();
			value.ikiTitle = '';
			value.ikiDescription = value.description;
			var match = value.description.match(titleRgex);
			if (match) {
				var cleanedText = $("<div/>").html(match[1]).text();
				cleanedText = (cleanedText) ? cleanedText.trim() : '';

				value.ikiDescription = '<span class="iki-lb-title">' + cleanedText + '</span>';
				value.ikiTitle = cleanedText;
			}

			value.ikiDescription += _self._createExternalLink(value.ikiExtLink, _self.extLinkText);

			_self.updateLightBox(value.ikiLargeSrc);
		});


		return imageData;
	}

	return app;

})(jQuery, window);