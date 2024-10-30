jQuery(document).ready(function ($) {

	"use strict";

	var searchPosts = Object.create(window.ikiToolkit.admin.SearchPosts);

	var $wrap = $('#iki-search-posts-wrap');
	searchPosts.init({
		postType: $wrap.data('ikiType'),
		optionName: $wrap.data('ikiOptionName'),
		ajaxNonce: $('#iki-ajax-nonce').data('ikiNonce'),
		$searchField: $('#iki-search-post'),
		$addBtn: $('#iki-add-post'),
		$foundContainer: $('#iki-found-posts'),
		$selectedContainer: $('#iki-selected-posts'),
		$spinner: $('#iki-spinner'),
		$postsNotFound: $('#iki-posts-not-found'),
		$postsError: $('#iki-posts-error')
	})

});

window.ikiToolkit = window.ikiToolkit || {};
window.ikiToolkit.admin = window.ikiToolkit.admin || {};
window.ikiToolkit.admin.SearchPosts = (function ($) {


	return {
		init: init,
		_displayResults: _displayResults,
		_sendRequest: _sendRequest,
		_sortList: _sortList
	};


	function init(data) {
		var _self = this;
		this.compiledResult = _.template('<span class="iki-post-result"><label><input data-iki-title="<%=title%>" ' +
			'data-iki-id="<%=id%>" data-iki-link="<%=link%>" type="checkbox"><%= title %></label></span>');

		this.compileSelected = _.template('<div class="iki-title-wrap iki-dynamic" data-iki-id="<%=id%>"><span class="content button iki-remove-post">X</span><p class="iki-post-title"><a href="<%=link%>" target="_blank"><%=title%></a></p><input type="hidden" name="<%=optionName%>[posts][<%=id%>]" value="<%=id%>" /></div>');

		this.postType = data.postType;
		this.optionName = data.optionName;
		this.$searchField = data.$searchField;
		this.$addBtn = data.$addBtn;
		this.$foundContainer = data.$foundContainer;
		this.$selectedContainer = data.$selectedContainer;
		this.$spinner = data.$spinner;
		this.ajaxNonce = data.ajaxNonce;
		this.url = ajaxurl;//magic
		this.$postsError = data.$postsError;
		this.$postsNotFound = data.$postsNotFound;

		this.requestTimestamp = Date.now();

		this.selectedPosts = {};

		var $selectedPosts = this.$selectedContainer.children();

		if ($selectedPosts.length) {
			_self._sortList();
			$selectedPosts.each(function () {
				_self.selectedPosts[$(this).data('ikiId')] = true;
			})
		}


		this.removePost = function (e) {

			e.preventDefault();
			var $this = $(this);
			delete _self.selectedPosts[$this.parent().data('ikiId')];
			$this.parent().remove();

		};
		this.$selectedContainer
			.find('.iki-remove-post')
			.on('click', this.removePost);

		this.$addBtn.on('click', function (e) {

			e.preventDefault();

			var $checked = _self.$foundContainer.find('input').filter(function () {
				var $this = $(this);
				return $this.attr('checked') && !_self.selectedPosts[$this.data('ikiId')];

			});

			if ($checked.length) {

				var $html = '';
				//create templates and put them in selected container
				$checked.each(function () {
					var $this = $(this);
					_self.selectedPosts[$this.data('ikiId')] = true;
					$html += _self.compileSelected({
						id: $this.data('ikiId'),
						title: $this.data('ikiTitle'),
						link: $this.data('ikiLink'),
						optionName: _self.optionName
					});
				});

				$html = $($html);

				_self.$selectedContainer.append($html);
				$html.find('.iki-remove-post').on('click', _self.removePost);
			}

		});

		this.debouncedRequest = _.debounce(this._sendRequest, 500);

		this.$searchField.on('keyup', function (e) {
			var v = $(this).val();
			//do the search
			_self.debouncedRequest(v, _.bind(_self._displayResults, _self));
		})
	}

	function _sendRequest(search, cb) {

		this.$spinner.css('visibility', 'visible');

		this.$postsNotFound.addClass('hidden');
		this.$postsError.addClass('hidden').find('.iki-server-error').empty();

		$.ajax({
			type: 'POST',
			url: this.url,
			dataType: 'json',
			timeout: 15000,
			data: {
				search: search,
				_ajax_nonce: this.ajaxNonce,
				time: Date.now(),
				action: 'iki_search_posts',
				post_type: this.postType
			}
		}).done(function (data, status, xhr) {
			cb(data, status, xhr)

		}).fail(function (data, status, xhr) {

			cb(data, status, xhr);
		});
	}

	function _displayResults(data, xhrStatus, xhr) {

		var _self = this;

		this.$spinner.css('visibility', 'hidden');
		this.$foundContainer.empty();

		if ('success' === xhrStatus && data.time > _self.requestTimestamp) {

			//ajax successufilly completed
			_self.requestTimestamp = data.time;
			//hide spinner
			if (data.posts.length) {
				//we have posts to display
				var html = '';
				_.forEach(data.posts, function (post) {

					html += _self.compiledResult({
						id: post.id,
						link: post.edit_post_link,
						title: post.title
					})
				});

				_self.$foundContainer.html(html);
				_self._sortList();
			}
			else {
				//no posts to display
				_self.$postsNotFound.removeClass('hidden');
			}
		} else {
			//error with ajax
			_self.$postsError.removeClass('hidden').find('.iki-server-error').html(xhr);
		}
	}

	function _sortList() {
		this.$selectedContainer.sortable({
			revert: true
		}).disableSelection();

		this.$selectedContainer.find('li').disableSelection();
	}

})(jQuery);
