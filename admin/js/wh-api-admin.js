(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	 $(function() {
		$(document).on('click', '#wh_api_update_JSON', function(e){
			var nonce = $(this).data('nonce');
			var button = $(this);
			var parent = $(this).parent();
			var spinner = '<span class="spinner is-active"></span>';
				button.addClass('button-disabled');
			var msg = '<p class="msg" style="color:green;">JSON updated.</p>';
			$(spinner).insertAfter(button);
			parent.find('.msg').remove();

			$.ajax({
				type	: 'POST',
				url 	: ajaxurl,
				data 	: {action: 'wh_api_update_json', nonce: nonce},
				success	: function(response){
					if (response.success) {
						parent.append(msg);
					}
					button.removeClass('button-disabled');
					parent.find('.spinner').remove();
	
				}
			});
		});
	 });
})( jQuery );
