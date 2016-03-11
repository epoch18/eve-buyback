$("#manage-form-motd").submit(function () {
	var button = $("#manage-form-motd #submit");

	button.attr("disabled", true);

	$.post($(this).attr("action"), $(this).serialize(), function(response) {
		button.attr("disabled", false);

		if (response.result == true) {
			$.notify(response.message, {
				className: "success",
			});
		} else {
			$.notify(response.message, {
				className: "error",
			});
		}
	});

	return false;
});
