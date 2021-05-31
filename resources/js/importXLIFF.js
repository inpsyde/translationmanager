(
	function ImportXLIFF( $ ) {
		'use strict';
		var init = false;

		/**
		 * Executes on AJAX success and append the menu markup received via AJAX, which is passed as parameter,
		 * to the current menu.
		 *
		 * @param {jQuery} $copyMenuSettingsMarkup
		 */
		var onAjaxSuccess = function ( data ) {

			if ( ! data.fileUrl ) {

				onAjaxError();
				return;
			}

		};

		/**
		 * Executes on AJAX error.
		 */
		var onAjaxError = function () {

			alert( 'AJAX error.' );
		};

		/**
		 * @param {number[]} ids
		 * @return {{action, mlp_sites: *, menu: *}}
		 */
		var ajaxData = function () {
			var ajaxAction = 'translationmanager_import_xliff';
			var data = {
				action: ajaxAction,
				projectId: projectInfo.projectId,
				ajaxAction
			};

			return data;
		};

		/**
		 * Send the AJAX request to update the menu for the given languages ids.
		 *
		 * @param {number[]} ids
		 */
		var sendRequest = function () {

			$.ajax( {
				url: ajaxurl,
				method: 'POST',
				dataType: 'json',
				data: ajaxData()
			} ).done( function ( response ) {
				if ( response.success && response.data ) {
					onAjaxSuccess(response.data);

					return;
				}

			} ).fail(function(xhr, status, error) {
				onAjaxError();
			})
		};

		/**
		 * Init the class by setting events callbacks on the jQuery element.
		 *
		 * @return {MultilingualPress.NewSiteLanguage}
		 */
		var initiate = function () {
			if ( ! init ) {
				$('#translationmanager-import-xliff').submit(function (e) {
					sendRequest();
					e.preventDefault();
				});

				init = true;
			}

			return this;
		};

		initiate();
	}( window.jQuery )
);
