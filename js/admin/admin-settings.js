jQuery(document).ready(function ($) {

	var $testBtn,
		$extSelect;

	if (!window.ikiToolkit) {
		return;
	}
	var extValidator = Object.create(window.ikiToolkit.admin.ExternalApiValidator).init();
	extValidator.setNonce(window.ikiToolkitExports.wpNonce);

	if ('undefined' !== typeof fwEvents) {

		fwEvents.on('fw:options:init', function (data) {

			if (!data.lazyTabsUpdated) {
				return;
			}

			if (!$testBtn) {

				$testBtn = $('#iki-test-ext-data');
				$extSelect = $('#fw-option-external_service-service');

				if ($testBtn.length) {
					initExtDataTest();
					initCacheDeletion();
				}
			}
		});
	}

	function initCacheDeletion() {


		var $delBtn = $('#iki-del-ext-data');
		var $successField = $('#iki-del-success');
		var $errorField = $('#iki-del-error');
		var $spinner = $('#iki-del-ext-spinner').css('float', 'none');

		var exportData = window.ikiToolkitExports;

		$delBtn.on('click', function () {

			$delBtn.prop('disabled', true);
			$spinner.css({
				'display': 'inline-block'
			});
			deleteExtServiceCache(exportData.post.id, exportData.wpNonce, ajaxurl).done(function (data) {

				$successField.show();
				$errorField.hide();

			}).fail(function (data) {
				$successField.hide();
				$errorField.show();

			}).always(function () {

				$spinner.css({
					'display': 'none'
				});

				$delBtn.prop('disabled', false);

			})
		});

	}

	function deleteExtServiceCache(postID, nonce, ajaxurl) {

		var deferred = $.Deferred();
		var data = {
			action: 'iki_delete_ext_cache',
			post_id: postID,
			_ajax_nonce: nonce
		};

		$.ajax({
			type: 'POST',
			url: ajaxurl,
			dataType: 'json',
			timeout: 10000,
			data: data
		}).done(function (data, status, xhr) {
			deferred.resolve();
		}).fail(function (data, status, xhr) {
			deferred.reject();
		});

		return deferred.promise();


	}

	function initExtDataTest() {

		var $successField = $('#iki-data-success');
		var $errorField = $('#iki-data-error');
		var timeoutText = $errorField.data('ikiTimeout');
		var $spinner = $('#iki-test-ext-spinner').css('float', 'none');

		$testBtn.on('click', function () {
			//check service
			var chosenService = $extSelect.val();
			var response;


			switch (chosenService) {
				case 'flickr' :
					var flickrId = $('#fw-option-external_service-flickr-username').val();
					var photosetId = $('#fw-option-external_service-flickr-photoset_id').val();
					response = extValidator.testFlickr(flickrId, photosetId);
					break;
				case 'pinterest' :
					var pinterestUsername = $('#fw-option-external_service-pinterest-username').val();
					var pinterestBoard = $('#fw-option-external_service-pinterest-boardname').val();
					response = extValidator.testPinterest(pinterestUsername, pinterestBoard);
					break;
			}

			$successField.hide('fast');
			$errorField.hide('fast');
			$spinner.css('visibility', 'visible');


			if (response) {

				$extSelect.prop('disabled', true);
				$testBtn.prop('disabled', true);
				response.done(function (data) {

					$errorField.text('');
					$successField.show();
					$successField.text(data.message);

				}).fail(function (data) {

					if (data.statusText && 'timeout' === data.statusText) {
						$errorField.text(timeoutText);
					} else {

						$errorField.text(data.message);
					}

					$successField.text('');
					$errorField.show();

				}).always(function () {

					$spinner.css('visibility', 'hidden');
					$extSelect.prop('disabled', false);
					$testBtn.prop('disabled', false);

				})
			}
		});
	}
});

jQuery(document).ready(function ($) {


});

window.ikiToolkit = window.ikiToolkit || {};
window.ikiToolkit.admin = window.ikiToolkit.admin || {};
window.ikiToolkit.admin.ExternalApiValidator = (function ($) {

	"use strict";

	return {

		testFlickr: testFlickr,
		testPinterest: testPinterest,
		sendRequest: sendRequest,
		init: init,
		setNonce: setNonce,
		_sendRequest: sendRequest

	};


	function init() {

		this.defaultData = {
			flickr: '81703997@N00',
			pinterest: ''
		};
		return this;
	}

	function setNonce(nonce) {
		this.nonce = nonce;
	}

	function testFlickr(username, photoset, apiKey, keyOnly) {

		if (keyOnly) {
			username = (username.trim()) ? username : this.defaultData.flickr;
		}

		var action = 'iki_check_external_data';
		var method = 'get_user';

		var d = {
			action: action,
			method: method,
			service: 'flickr',
			data: {
				'user_id': username,
				'cache': 'disabled'
			}
		};

		if (apiKey) {
			d.data.api_key = apiKey;
		}
		if (photoset.trim()) {
			d.method = 'get_photoset_info';
			d.data.photoset_id = photoset;
		}

		return this._sendRequest(d);
	}


	function testPinterest(username, board) {

		username = (username.trim()) ? username : this.defaultData.pinterest;

		var action = 'iki_check_external_data';
		var method = 'get_user_latest_pins';

		var d = {
			action: action,
			method: method,
			service: 'pinterest',
			data: {
				'user': username,
				'cache': 'disabled'
			}
		};

		if (board.trim()) {
			d.method = 'get_user_board';
			d.data.boardname = board;
		}

		return this._sendRequest(d);
	}

	function sendRequest(data) {


		data._ajax_nonce = this.nonce;

		var deferred = $.Deferred();
		$.ajax({
			type: 'POST',
			url: ajaxurl,
			dataType: 'json',
			timeout: 15000,
			data: data
		}).done(function (data, status, xhr) {

			if (0 === data) {

				deferred.reject(data);

			} else {

				data = JSON.parse(data);

				if (!data || 'failure' === data.status) {
					deferred.reject(data);
				}
				else {
					deferred.resolve(data);
				}
			}


		}).fail(function (data, status, xhr) {
			try {
				data = JSON.parse(data);
			} catch (e) {

			}
			deferred.reject(data, status, xhr);
		});

		return deferred.promise();
	}
}(jQuery));

jQuery(document).ready(function ($) {

	"use strict";
	var extValidator = Object.create(window.ikiToolkit.admin.ExternalApiValidator).init();
	// flickr api test
	var ajaxNonce = $('#iki-ajax-nonce').data('ikiNonce');
	extValidator.setNonce(ajaxNonce);

	var $flickrInput = $('#iki-flickr_api_key');
	var $flickrUI = $('#iki-test-flickr-api');

	var $flickrTestBtn = $flickrUI.children('.button');
	var $flickrSpinner = $flickrUI.children('.spinner');
	var $flickrSuccessField = $flickrUI.children('.updated');
	var $flickrErrorField = $flickrUI.children('.error');

	$flickrTestBtn.on('click', function (e) {

		e.preventDefault();
		$flickrTestBtn.prop('disabled', true);

		$flickrSuccessField.hide();
		$flickrErrorField.hide();

		var apiValue = $flickrInput.val();
		apiValue = (apiValue.trim()) ? apiValue : 'fake_key_force_error';
		var response = extValidator.testFlickr('', '', apiValue, true);

		response.always(function () {
			$flickrTestBtn.prop('disabled', false);
		});

		handleResponse(response, $flickrSpinner, $flickrSuccessField, $flickrErrorField);
	});

	function handleResponse(response, $spinner, $successField, $errorField) {

		$spinner.css('visibility', 'visible');

		response.done(function (data) {

			$successField.show();
			$successField.text($spinner.data('ikiSuccess'));

		}).fail(function (data) {

			if (data.status && 'failure' === data.status) {

				$errorField.text(data.message);
			}
			else if (data.statusText && 'timeout' === data.statusText) {

				$errorField.text($spinner.data('ikiTimeout'));

			} else {

				$errorField.text($spinner.data('ikiFailure'));
			}

			$successField.text('');
			$errorField.show();

		}).always(function () {
			$spinner.css('visibility', 'hidden');
		});

	}


});
