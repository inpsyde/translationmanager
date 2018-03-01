/**
 * Manage Posts
 *
 * @since 1.0.0
 */

(
	function managePosts( $ ) {
		'use strict';

		$.document( 'ready', function managePostsCallback() {
			$( '.tablenav .actions.bulkactions' ).on( 'click', '.button.action', function ( e ) {
				var selectVal = $( this ).prev( 'select' ).val();

				if ( 'bulk_translate' === selectVal ) {
					e.preventDefault();
					var checked = $( "input[name='post[]']" ).is( ':checked' );

					if ( ! checked ) {
						alert( 'You must need to select at least one element for translate.' );
					} else {
						var buttonID = $( this ).attr( 'id' );
						var $el      = null;

						if ( 'doaction' === buttonID ) {
							$el = $( this )
								.parent()
								.parent()
								.find( '.translationmanager-language-overlay' );
						} else {
							$el = $( this )
								.parent()
								.parent()
								.parent()
								.find( '.tablenav.top' )
								.find( '.translationmanager-language-overlay' );
						}

						$el
							.css( {
								visibility: 'visible',
								opacity   : 1
							} );
					}

					return true;
				}
			} );

			$( '.translationmanager-language-overlay .translationmanager-lang-popup' ).on( 'click', '.close', function () {
				$( this ).parent().parent().removeAttr( 'style' );
			} );

			$( '#translationmanager-lang-wrap-div' ).on( 'change', 'input[type=checkbox]', function () {
				var inputStatus = $( this ).parent().find( 'input[name="translationmanager_bulk_languages[]"]' ).is( ':checked' );

				if ( ! inputStatus ) {
					$( '#translationmanager-submit-bulk-translate' ).attr( 'disabled', true );
				}

				return true;
			} );
		} );
	}( window.jQuery )
);
