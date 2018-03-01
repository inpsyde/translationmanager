/**
 * options-page.js
 *
 * @since 1.0.0
 */

(
	function optionsPage( $ ) {
		'use strict';

		// Retrieve tabs elements.
		var tabs = $( '#inpsyde-tabs' );
		if ( ! tabs ) {
			return;
		}

		tabs.tabs( {
			activate: function tabsActivate( event, ui ) {
				var $form = $( '#inpsyde-form' );

				if ( ! $form ) {
					return;
				}

				var $anchor = event.currentTarget;
				var hash    = $anchor.getAttribute( 'href' );
				var action  = $form.attr( 'action' ).split( '#' )[ 0 ];

				$form.attr( 'action', action + hash );
			}
		} );
	}( window.jQuery )
);
