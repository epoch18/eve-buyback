document.onkeydown = function(evt) {
	evt = evt || window.event;
	if (evt.ctrlKey && evt.keyCode == 86) {
		var input = document.getElementById("pasteDataTextBox");
		input.focus();

		setTimeout(function() {
			form = document.getElementById("pasteForm");
			form.submit();
		}, 500);
	}
};
