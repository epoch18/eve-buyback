@extends('layouts.main')

@section('styles')
@parent
@endsection

@section('content')
<form method="POST" action="{!! route('paste') !!}" id="pasteForm">
	<div style="width:0;height:0;overflow:hidden">
		<input type="hidden" name="_token" value="{!! csrf_token() !!}">
		<textarea id="pasteDataTextBox" name="pasteData" style="opacity:0;filter:alpha(opacity=0);"></textarea>
		<input type="submit" style="opacity:0;filter:alpha(opacity=0);" name="pasteSubmit">
	</div>
</form>

<div class="col-md-offset-2 col-md-8">

	<div class="col-md-12">

		<div class="panel">
			<div class="panel-body">
				<h5>{!! trans('buyback.headers.mining_prices') !!}</h5>
			</div>
			<div class="panel-body table-responsive">
				<table id="mining-table" class="table table-condensed table-striped table-hover">
					<thead>
						<tr>
							<th>{!! trans('buyback.headers.name'                ) !!}</th>
							<th>{!! trans_choice('buyback.headers.groups'    , 1) !!}</th>
							<th>{!! trans_choice('buyback.headers.categories', 1) !!}</th>
							<th>{!! trans('buyback.headers.price'               ) !!}</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th>{!! trans('buyback.headers.name'                ) !!}</th>
							<th>{!! trans_choice('buyback.headers.groups'    , 1) !!}</th>
							<th>{!! trans_choice('buyback.headers.categories', 1) !!}</th>
							<th>{!! trans('buyback.headers.price'               ) !!}</th>
						</tr>
					</tfoot>
					<tbody>
						@foreach ($asteroids as $asteroid)
						<tr>
							<td><img src="https://image.eveonline.com/Type/{!! $asteroid['typeID'] !!}_32.png"> {!! $asteroid['typeName'] !!}</td>
							<td>{!! $asteroid['groupName'   ] !!}</td>
							<td>{!! $asteroid['categoryName'] !!}</td>
							<td>{!! number_format($asteroid['price'], 2, '.', ',') !!} ISK</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>

	</div>

</div>
@endsection

@section('scripts')
@parent
<script type="text/javascript">
	$(document).ready(function() {
		$('[data-toggle="tooltip"]').tooltip();

		initMiningTable({
			actions: {
				getAsteroids: "{!! route('home.mining.asteroids') !!}",
			},
			token: "{!! csrf_token() !!}",
			trans: {!! $trans !!},
		});
	});
</script>
@endsection
