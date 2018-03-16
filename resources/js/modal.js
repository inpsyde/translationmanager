(
	function modal( _, $ ) {
		'use strict';

		var Modal = {

			/**
			 * Is Open
			 *
			 * @since 1.0.0
			 *
			 * @returns {boolean} True if the modal is open, false otherwise
			 */
			isOpen: function () {
				return this.currentModal && this.currentModal.classList.contains( 'is-open' );
			},

			/**
			 * Open the modal
			 *
			 * @since 1.0.0
			 *
			 * @param {Event} evt The event object triggered to open the modal
			 */
			open: function ( evt ) {
				var that = this;

				evt.preventDefault();
				evt.stopPropagation();

				if ( this.isOpen() ) {
					return;
				}

				$( evt.currentTarget.getAttribute( 'href' ) ).parent().fadeIn( function showModal() {
					var currentModal = this.children[ 0 ];
					that.currentModal = currentModal;

					that.currentModal.classList.add( 'is-open' );

					$( currentModal ).fadeIn();

					this.addEventListener( 'click', that.close );
				} );
			},

			/**
			 * Close the modal
			 *
			 * @since 1.0.0
			 *
			 * @param {Event} evt The event object triggered to close the modal
			 */
			close: function ( evt ) {
				evt.preventDefault();
				evt.stopPropagation();

				if ( ! this.currentModal ) {
					return;
				}

				$( this.currentModal ).fadeOut( function hideOverlay() {
					this.currentModal.parentNode.removeEventListener( 'click', this.close );

					$( this.currentModal ).parent().fadeOut();

					this.currentModal.classList.remove( 'is-open' );

					this.currentModal = null;
				}.bind( this ) );
			},

			/**
			 * Initialize Object
			 *
			 * @since 1.0.0
			 *
			 * @returns {Modal} this For concatenation
			 */
			init: function () {
				_.forEach( this.triggers, function loopTriggers( item ) {
					item.addEventListener( 'click', this.open );
				}.bind( this ) );

				return this;
			},

			/**
			 * Construct
			 *
			 * @since 1.0.0
			 *
			 * @param {HTMLCollection} triggers The triggers collection
			 *
			 * @returns {Modal} this For concatenation
			 */
			construct: function ( triggers ) {
				_.bindAll(
					this,
					'isOpen',
					'open',
					'close',
					'init'
				);

				if ( ! triggers.length ) {
					return false;
				}

				this.triggers = triggers;
				this.currentModal = null;

				return this;
			}
		};

		/**
		 * Modal Factory
		 *
		 * @since 1.0.0
		 *
		 * @constructor
		 *
		 * @returns {Modal} A new Modal object
		 */
		var ModalFactory = function () {
			return Object.create( Modal ).construct( document.querySelectorAll( '.modal-trigger' ) );
		};

		window.addEventListener( 'load', function windowModalLoader() {
			var modalInstance = ModalFactory();

			modalInstance && modalInstance.init();
		} );
	}( window._, window.jQuery )
);
