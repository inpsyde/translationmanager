/**
 * Manage Posts
 *
 * @since 1.0.0
 */

(
	function managePosts( _, $, strings ) {
		'use strict';

		/**
		 * Bulk Translate
		 *
		 * @since 1.0.0
		 *
		 * @type {{enableElement: enableElement, disableElement: disableElement, toggleBulkTranslateSubmitBasedOnCheckboxesValues: toggleBulkTranslateSubmitBasedOnCheckboxesValues, showLayer: showLayer, hideLayer: hideLayer, enableDisableSubmit: enableDisableSubmit, init: init, construct: construct}}
		 */
		var BulkTranslate = {

			/**
			 * Enable Elements
			 *
			 * @since 1.0.0
			 *
			 * @params element The element to enable.
			 */
			enableElement: function ( element ) {
				element.removeAttribute( 'disabled' );
			},

			/**
			 * Disable Element
			 *
			 * @since 1.0.0
			 *
			 * @params element The element to disable.
			 */
			disableElement: function ( element ) {
				element.setAttribute( 'disabled', true );
			},

			/**
			 * Toggle the Bulk translate submit based on the checked languages
			 *
			 * @since 1.0.0
			 */
			toggleBulkTranslateSubmitBasedOnCheckboxesValues: function () {
				var count;
				var $languages = $( '#translationmanager-lang-wrap-div' ).find( 'input[type=checkbox]' );

				var enabled = false;
				for ( count = 0; count < $languages.length; ++ count ) {
					if ( $( $languages[ count ] ).is( ':checked' ) ) {
						this.enableElement( this.submitBtn );
						enabled = true;
						break;
					}
				}

				if ( ! enabled ) {
					this.disableElement( this.submitBtn );
				}
			},

			/**
			 * Show Submit Layer
			 *
			 * @since 1.0.0
			 *
			 * @param {Event} evt The current event.
			 */
			showLayer: function ( evt ) {
				var selectVal = $( evt.currentTarget ).prev( 'select' ).val();
				var checked   = $( "input[name='post[]']" ).is( ':checked' );

				if ( 'bulk_translate' === selectVal ) {
					evt.preventDefault();

					this.toggleBulkTranslateSubmitBasedOnCheckboxesValues();

					if ( ! checked ) {
						alert( strings.noElementsSelected );

						return;
					}

					if ( 'doaction' !== evt.currentTarget.getAttribute( 'id' ) ) {
						return;
					}

					$( this.overlay ).css( {
						visibility: 'visible',
						opacity   : 1
					} );
				}
			},

			/**
			 * Hide Layer
			 *
			 * @since 1.0.0
			 *
			 * @param {Event} evt The current event.
			 */
			hideLayer: function ( evt ) {
				$( evt.currentTarget ).parent().parent().removeAttr( 'style' );
			},

			/**
			 * Enable Disable Submit
			 *
			 * @since 1.0.0
			 *
			 * @param {Event} evt The current event.
			 */
			enableDisableSubmit: function ( evt ) {
				var inputStatus = $( evt.currentTarget )
					.parent()
					.find( 'input[name="translationmanager_bulk_languages[]"]' )
					.is( ':checked' );

				if ( inputStatus ) {
					this.enableElement( this.submitBtn );

					return;
				}

				this.disableElement( this.submitBtn );
			},

			/**
			 * Init
			 *
			 * @since 1.0.0
			 *
			 * @returns {BulkTranslate} For concatenation
			 */
			init: function () {
				$( '.tablenav .actions.bulkactions' ).on( 'click', '.button.action', this.showLayer );
				$( '.translationmanager-language-overlay .translationmanager-lang-popup' ).on( 'click', '.close', this.hideLayer );
				$( '#translationmanager-lang-wrap-div' ).on( 'change', 'input[type=checkbox]', this.enableDisableSubmit );

				return this;
			},

			/**
			 * Construct
			 *
			 * @since 1.0.0
			 *
			 * @returns {*} {BulkTranslate} For concatenation or false if something went wrong.
			 */
			construct: function () {
				_.bindAll(
					this,
					'init',
					'enableElement',
					'disableElement',
					'showLayer',
					'hideLayer',
					'enableDisableSubmit',
					'toggleBulkTranslateSubmitBasedOnCheckboxesValues'
				);

				this.overlay = document.getElementById( 'translationmanager_language_overlay' );

				if ( ! this.overlay ) {
					return false;
				}

				this.submitBtn = document.getElementById( 'translationmanager_submit_bulk_translate' );

				return this;
			}
		};

		/**
		 * Factory
		 *
		 * @constructor
		 *
		 * @returns {*}
		 */
		var BulkTranslateFactory = function () {
			return Object.create( BulkTranslate ).construct();
		};

		$( document ).ready( function () {
			var instance = BulkTranslateFactory();
			if ( instance ) {
				instance.init();
			}
		} );
	}( window._, window.jQuery, window.strings )
);
