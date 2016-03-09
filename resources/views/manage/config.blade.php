@extends('layouts.main')

@section('content')
<div class="col-md-offset-2 col-md-8">

	<div class="col-md-12">
		<div class="panel">
			<div class="panel-heading">
				<h5>{!! trans('buyback.config.motd.header') !!}</h5>
			</div>
			<div class="panel-body">
				<form action="{!! route('config.motd') !!}" method="POST" name="motd">
					<input type="hidden" name="_token" value="{!! csrf_token() !!}">
					<div class="form-group">
						<textarea class="form-control" rows="10" name="text">{!! $motd !!}</textarea>
					</div>
					<div class="form-group">
						<button class="btn btn-default" id="submit" style="float: right;">{!! ucfirst(trans('buyback.config.motd.submit')) !!}</button>
					</div>
				</form>
			</div>
			<div class="panel-footer">
				<small>{!! trans('buyback.config.motd.footer') !!}</small>
			</div>
		</div>
	</div>

</div>
@endsection

@section('scripts')
@parent
<script type="text/javascript">
	$('form[name=motd]').submit(function () {
		var button = $('form[name=motd] #submit');
		button.attr('disabled', true);

		$.post($(this).attr('action'), $(this).serialize(), function(response) {
			button.attr('disabled', false);

			if (response.result == true) {
				button.notify(response.message, {className: 'success', position: 'right'});
			} else {
				button.notify(response.message, {className: 'error'  , position: 'right'});
			}
		});

		return false;
	});
</script>
@endsection
