function initManageMotd(args) {
	var form = $("#manage-form-motd");

	form.submit(function () {
		var button = $("#manage-form-motd #submit");

		button.prop('disabled', true);
		button.html('<span class="fa fa-spinner fa-spin"></span> ' + args.trans.buttons.edit);

		$.ajax({
			type: "POST",
			url: args.actions.editMotd,
			data: form.serialize(),

			success: function(response) {
				$.notify(response.message, {className: 'success'});

				button.prop('disabled', false);
				button.html(args.trans.buttons.edit);
			},

			error: function(request, status, error) {
				var response = JSON.parse(request.responseText);

				$.notify(response.message, {className: 'error'});

				button.prop('disabled', false);
				button.html(args.trans.buttons.edit);
			},
		});

		return false;
	});
}
