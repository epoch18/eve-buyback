@extends('layouts.main')

@section('content')
<form method="POST" action="{!! route('paste') !!}" id="pasteForm">
	<div style="width:0;height:0;overflow:hidden">
		<input type="hidden" name="_token" value="{!! csrf_token() !!}">
		<textarea id="pasteDataTextBox" name="pasteData" style="opacity:0;filter:alpha(opacity=0);">
		</textarea>
	</div>
</form>
@endsection

@section('scripts')
	@parent
	<script>
		document.onkeydown = function(evt) {
			evt = evt || window.event;
			if (evt.ctrlKey && evt.keyCode == 86) {
				document.getElementById("pasteDataTextBox").focus();
				setTimeout(function() {
					inp = document.getElementById("pasteDataTextBox");
					//inp.value = inp.value.replace(/(?:\r\n|\r|\n)/g, '|');
					form = document.getElementById("pasteForm");
					form.submit();
				}, 500);
			}
		};
	</script>
@endsection
