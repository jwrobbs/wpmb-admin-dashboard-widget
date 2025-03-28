(function ($) {
	console.log("Cleanup History script loaded.");
	console.log(CleanHistoryAjax);
	$(document).ready(function () {
		$(".wpmb-clean-simple-history").on("click", function () {
			const $btn = $(this);
			const scope = $btn.data("scope");
			const nonce = $btn.data("nonce");
			const $msg = $("#simple-history-clean-response");

			$btn.prop("disabled", true);
			$msg.text("Cleaning...");

			$.post(
				CleanHistoryAjax.ajax_url,
				{
					action: CleanHistoryAjax.action,
					scope: scope,
					_wpnonce: nonce,
				},
				function (response) {
					if (response.success) {
						$msg.text("Cleanup complete: " + response.data.message);
					} else {
						$msg.text("Error: " + response.data);
					}
					$btn.prop("disabled", false);
				}
			).fail(function () {
				$msg.text("AJAX request failed.");
				$btn.prop("disabled", false);
			});
		});
	});
})(jQuery);
