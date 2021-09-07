(
	function exportXLIFF( $ ) {
		'use strict';
		var init = false;

		/**
		 * Executes on AJAX success and downloads the Xliff file
		 *
		 * @param data
		 */
		var onAjaxSuccess = function ( data ) {

			if ( ! data.fileUrl ) {

				onAjaxError();
				return;
			}
			download(data.fileUrl, data.fileName);

		};

		var download = function (url, filename) {
			fetch(url).then(function (response) {
				return response.blob().then((blob) => {
						var a = document.createElement("a");
						a.href = URL.createObjectURL(blob);
						a.setAttribute("download", filename);
						a.click();
					}
				);
			});
		}

		/**
		 * Executes on AJAX error.
		 */
		var onAjaxError = function () {

			alert( 'AJAX error.' );
		};

		var ajaxData = function () {
			var ajaxAction = 'translationmanager_export_xliff';
			var data = {
				action: ajaxAction,
				projectId: projectInfo.projectId
			};

			return data;
		};

		/**
		 * Send the AJAX request to generate the Xliff file and download it
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
		 */
		var initiate = function () {
			if ( ! init ) {
				$('#export-XLIFF').click(function (e) {
					sendRequest();
					return false;
				});

				init = true;
			}

			return this;
		};

		initiate();
	}( window.jQuery )
);
