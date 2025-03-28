(function ($) {
	jQuery(document).ready(function ($) {
		$("#toggle-debug-button").on("click", function () {
			const $btn = $(this);
			const nonce = $btn.data("nonce");
			const $status = $("#debug-status");
			const $msg = $("#debug-response-msg");

			$btn.prop("disabled", true);
			$msg.text("Toggling...");

			$.post(
				DebugToggleAjax.ajax_url,
				{
					action: DebugToggleAjax.action,
					_wpnonce: nonce,
				},
				function (response) {
					if (response.success) {
						$("#WP_DEBUG-value").text(
							response.data.status ? "Enabled" : "Disabled"
						);
						$("#WP_DEBUG_LOG-value").text(
							response.data.log ? "Enabled" : "Disabled"
						);
						$("#WP_DEBUG_DISPLAY-value").text(
							response.data.display ? "Enabled" : "Disabled"
						);
						$("#toggle-debug-button").text(
							response.data.status ? "Disable Debugging" : "Enable Debugging"
						);
					} else {
						$msg.text("Error: " + response.data);
					}
					$btn.prop("disabled", false);
				}
			).fail(function () {
				console.log("AJAX request failed when toggling debug.");
				$btn.prop("disabled", false);
			});
		});

		$("#delete-debug-log").on("click", function () {
			const $btn = $(this);
			const nonce = $btn.data("nonce");
			const $msg = $("#delete-debug-msg");

			$btn.prop("disabled", true);
			$msg.text("Deleting...");

			$.post(
				DebugToggleAjax.ajax_url,
				{
					action: "wpmb_delete_debug_log",
					_wpnonce: nonce,
				},
				function (response) {
					if (response.success) {
						$msg.text("Deleted!");
						$btn.remove();
					} else {
						$msg.text("Error: " + response.data);
						$btn.prop("disabled", false);
					}
				}
			);
		});
	});
})(jQuery);
