@extends('layouts.main')

@section('content')
<form method="POST" action="{!! route('paste') !!}" id="pasteForm">
	<div style="width:0;height:0;overflow:hidden">
		<input type="hidden" name="_token" value="{!! csrf_token() !!}">
		<textarea id="pasteDataTextBox" name="pasteData" style="opacity:0;filter:alpha(opacity=0);">
		</textarea>
	</div>
</form>

<div class="col-md-offset-3 col-md-6">
	<div class="panel">
		<div class="panel-body">
			<b><center>{!! trans('buyback.instructions') !!}</center></b>
		</div>
	</div>
</div>

@if (isset($buyback))
	<div class="col-md-offset-3 col-md-6">

		@if (count($buyback->unwanted) > 0)
			<div class="panel">
				<div class="panel-heading">
					<h3 class="panel-title"><b><center>{!! trans('buyback.unwanted_items') !!}</center></b></h3>
				</div>
				<div class="panel-body">
					<table id="unwanted" class="table table-condensed table-striped table-hover">
						<thead>
							<tr>
								<th>{!! ucfirst(trans('buyback.name'    )) !!}</th>
								<th>{!! ucfirst(trans('buyback.group'   )) !!}</th>
								<th>{!! ucfirst(trans('buyback.category')) !!}</th>
								<th>{!! ucfirst(trans('buyback.quantity')) !!}</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($buyback->unwanted as $item)
								<tr>
									<td><img src="https://image.eveonline.com/Type/{!! $item->type->typeID !!}_32.png"> {!! $item->type->typeName !!}</td>
									<td>{!! $item->type->group->groupName               !!}</td>
									<td>{!! $item->type->group->category->categoryName  !!}</td>
									<td>{!! number_format($item->quantity, 0, '.', ',') !!}</td>
								</tr>
							@endForeach
						</tbody>
					</table>
				</div>
			</div>
		@else
			<div class="panel">
				<div class="panel-heading">
					<center><h4>{!! trans('buyback.contract_total', ['total' => number_format($buyback->totalModded, 2, '.', '')]) !!}</h4></center>
				</div>
			</div>

			<div class="panel">
				<div class="panel-body">
					<h3 class="panel-title"><b><center>{!! ucfirst(trans('buyback.acceptable')) !!}</center></b></h3>
				</div>
				<div class="panel-body">
					<table id="wanted" class="table table-condensed table-striped table-hover">
						<thead>
							<tr>
								<th>{!! ucfirst(trans_choice('buyback.name'    , 1)) !!}</th>
								<th>{!! ucfirst(trans_choice('buyback.group'   , 1)) !!}</th>
								<th>{!! ucfirst(trans_choice('buyback.category', 1)) !!}</th>
								<th>{!! ucfirst(trans_choice('buyback.quantity', 1)) !!}</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($buyback->raw as $item)
								<tr>
									<td><img src="https://image.eveonline.com/Type/{!! $item->type->typeID !!}_32.png"> {!! $item->type->typeName !!}</td>
									<td>{!! $item->type->group->groupName              !!}</td>
									<td>{!! $item->type->group->category->categoryName !!}</td>
									<td>{!! number_format($item->quantity, 0)          !!}</td>
								</tr>
							@endForeach
							@foreach ($buyback->recycled as $item)
								<tr>
									<td><img src="https://image.eveonline.com/Type/{!! $item->type->typeID !!}_32.png"> {!! $item->type->typeName !!}</td>
									<td>{!! $item->type->group->groupName              !!}</td>
									<td>{!! $item->type->group->category->categoryName !!}</td>
									<td>{!! number_format($item->quantity, 0)          !!}</td>
								</tr>
							@endForeach
							@foreach ($buyback->refined as $item)
								<tr>
									<td><img src="https://image.eveonline.com/Type/{!! $item->type->typeID !!}_32.png"> {!! $item->type->typeName !!}</td>
									<td>{!! $item->type->group->groupName              !!}</td>
									<td>{!! $item->type->group->category->categoryName !!}</td>
									<td>{!! number_format($item->quantity, 0)          !!}</td>
								</tr>
							@endForeach
						</tbody>
					</table>
				</div>
			</div>

			<div class="panel">
				<div class="panel-body">
					<h3 class="panel-title"><b><center>{!! ucfirst(trans('buyback.breakdown')) !!}</center></b></h3>
				</div>
				<div class="panel-body">
					<table id="breakdown" class="table table-condensed table-striped table-hover">
						<thead>
							<tr>
							<tr>
								<th>{!! ucfirst(trans_choice('buyback.name'      , 1)) !!}</th>
								<th>{!! ucfirst(trans_choice('buyback.quantity'  , 1)) !!}</th>
								<th>{!! ucfirst(trans       ('buyback.unit_price'   )) !!}</th>
								<th>{!! ucfirst(trans       ('buyback.payout'       )) !!}</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($buyback->raw as $item)
								<tr>
									<td><img src="https://image.eveonline.com/Type/{!! $item->type->typeID !!}_32.png"> {!! $item->type->typeName !!}</td>
									<td>{!! number_format($item->quantity     , 0, '.', ',') !!}</td>
									<td>{!! number_format($item->buyUnitModded, 2, '.', ',') !!}</td>
									<td>{!! number_format($item->buyModded    , 2, '.', ',') !!}</td>
								</tr>
							@endForeach
							@foreach ($buyback->materials as $item)
							@if ($item->quantity > 0)
								<tr>
									<td><img src="https://image.eveonline.com/Type/{!! $item->type->typeID !!}_32.png"> {!! $item->type->typeName !!}</td>
									<td>{!! number_format($item->quantity     , 0, '.', ',') !!}</td>
									<td>{!! number_format($item->buyUnitModded, 2, '.', ',') !!}</td>
									<td>{!! number_format($item->buyModded    , 2, '.', ',') !!}</td>
								</tr>
							@endif
							@endForeach
						</tbody>
					</table>
				</div>
			</div>
		@endif

	</div>
@else
	<div class="col-md-offset-3 col-md-6">
		<div class="panel">
			<div class="panel-heading">
				<h3 class="panel-title"><b><center>news</center></b></h3>
			</div>
			<div class="panel-body">
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed venenatis tellus at consectetur blandit. Mauris sit amet eros eget dui efficitur auctor eu iaculis tellus. Morbi quis ligula nec dui consectetur ornare eu vitae nisl. Aenean id ligula vitae lacus aliquet vestibulum et id diam. Sed a lectus rutrum, tristique orci vitae, vestibulum est. Nullam consectetur odio ut odio gravida, a dictum lectus vehicula. Vestibulum ut posuere urna, vitae suscipit lacus. Vivamus in porttitor felis. Duis congue sollicitudin purus id eleifend.</p>
				<p>Donec eget blandit nulla. Nullam at sem dui. Praesent congue velit id leo vestibulum, nec aliquet orci placerat. Nam dictum varius elementum. Aenean lacinia nisi ac lacus tempor, at gravida turpis ullamcorper. Praesent mollis rhoncus sapien sed venenatis. Mauris molestie arcu augue, id vestibulum augue sagittis vel. Duis venenatis, odio nec sagittis gravida, purus neque pellentesque nunc, nec ultricies massa sapien non est. Quisque sit amet lacinia nisi. Nulla viverra ex tristique nulla tincidunt semper. Etiam tristique et nisi eu fringilla.</p>
				<p>Quisque rutrum ac velit et ornare. Mauris eget elementum purus. Integer luctus vehicula quam, eu hendrerit massa hendrerit eget. Maecenas at lacinia erat. Donec vitae tristique arcu. Sed ullamcorper odio non accumsan feugiat. Curabitur orci risus, vehicula ut suscipit non, dictum condimentum sem. Nulla facilisi. Morbi eget dui id nisl dignissim pulvinar. Integer lacinia malesuada pulvinar. Integer ac commodo urna.</p>
			</div>
		</div>
	</div>
@endif

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

		$(document).ready(function() {
			$('#unwanted' ).DataTable();
			$('#wanted'   ).DataTable();
			$('#breakdown').DataTable();
		});
	</script>
@endsection
