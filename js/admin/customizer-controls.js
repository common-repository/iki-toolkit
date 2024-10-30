jQuery(document).ready(function ($) {

	$('.iki-fs-up-demo').on('click', function (e) {
		e.preventDefault();
		wp.customize.previewer.send('iki_active_panel_id', $(this).attr('value'));
	});

	//respond to active section id
	wp.customize.previewer.bind('iki_get_active_panel_id', function () {

		var activePanelId = $('.accordion-section').filter('.current-panel').attr('id');
		wp.customize.previewer.send('iki_active_panel_id', activePanelId);

	});

	//handle class mutations for fs panel sections
	function classMutationCallback(allMutations, a, b) {

		allMutations.map(function (mutation) {
			if ('class' === mutation.attributeName) {

				var $target = $(mutation.target);
				var payload = 'iki-' + $target.attr('id').substr(16).replace(/_/g, '-');

				if ($target.hasClass('current-panel')) {
					// focus the panel
					wp.customize.previewer.send('iki_fs_panel_focus', payload);
				}
				else {
					// remove the panel
					console.log("remove panel");
					wp.customize.previewer.send('iki_fs_panel_unfocus', payload);
				}
			}
		});
	}


	if (window.MutationObserver) {
		// setup mutation observer for fs panels.
		var $fsPanelSections = $([]);
		var mutationOptions = {
			'attributes': true
		};


		for (var i = 1; i <= 10; i++) {
			$fsPanelSections = $fsPanelSections.add($('#accordion-panel-fs_panel_' + i));
		}


		if ($fsPanelSections.length) {
			// setup mutation observers
			$fsPanelSections.each(function () {
				var mo = new MutationObserver(classMutationCallback);
				mo.observe(this, mutationOptions);
			})
		}
	}


});
