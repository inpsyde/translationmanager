(
	function ImportXLIFF( $ ) {
		'use strict';
		var init = false;

		/**
		 * Executes on AJAX error.
		 */
		var onAjaxError = function (error) {
			alert(error);
		};

		/**
		 * Executes on AJAX success
		 */
		var onAjaxSuccess = function () {
			alert('translations are imported');
			$('#xliff-file').val('');
		};

		/**
		 * Send the AJAX request to import the Xliff data
		 */
		var sendRequest = function () {
			var ajaxAction = 'translationmanager_import_xliff';

			var fileToImport = $('#xliff-file').prop('files');
			if (!fileToImport.length > 0) {
				alert("Please select a file.");
				return false;
			}

			var data = new FormData();
			data.append('file', fileToImport[0] );
			data.append('action', ajaxAction);
			data.append('projectId', projectInfo.projectId);

			$.ajax( {
				url: ajaxurl,
				method: 'POST',
				data: data,
				processData: false,
				contentType: false,
			} ).done( function ( response ) {
				if ( response.success && response.data ) {
					onAjaxSuccess(response.data);
					return;
				}
				onAjaxError(response.data);

			} ).fail(function(xhr, status, error) {
				onAjaxError(error);
			})
		};

		/**
		 * Init the class by setting events callbacks on the jQuery element.
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
