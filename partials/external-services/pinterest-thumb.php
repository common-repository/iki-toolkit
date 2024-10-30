<?php
/**
 * Thumb template/partial that is used for Pinterest external service.
 * This is used via javascript to construct the thumb with all appropriate data for particular service
 */
?>

<script type="text/template" id="iki-thumb-template" data-instagram-template>
	<div id="iki-thumb-<%= ikiThumbId %>" class="iki-thumb-container"
		 data-iki-large-src="<%= ikiLargeSrc %>">
		<div class="iki-thumb-title"><%= ikiTitle %></div>
		<div class="iki-info">
			<div class="iki-desc"><%= ikiDescription %></div>
			<div class="iki-links">
				<a href="<%= ikiLargeSrc %>" data-iki-thumb-index=""
				   class="iki-view-larger iki-link"
				   target="_blank"
				   data-iki-lightbox><span
						class="iki-icon-eye "></span></a>
			</div>
		</div>
		<div class="iki-img-wrap iki-pin-img embed-responsive embed-responsive-portrait"
			 style="background-image: url(<%= ikiThumbSrc %>);">
			<img class="iki-hide" src="<%= ikiThumbSrc %>">
		</div>
	</div>
</script>