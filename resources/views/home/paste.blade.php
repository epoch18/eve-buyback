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
				<b><center>{!! trans('buyback.instructions') !!}</center></b>
			</div>
		</div>
	</div>

</div>

<div class="col-md-offset-2 col-md-8">

	@if (count($buyback->unwanted) > 0)
		<div class="col-md-12">
			<div class="panel">
				<div class="panel-heading" style="text-align: center;">
					<h5>{!! ucfirst(trans('buyback.unwanted')) !!}</h5>
				</div>
				<div class="panel-body">
					<table id="unwanted" class="table table-condensed table-striped table-hover">
						<thead>
							<tr>
								<th>{!! ucfirst(trans_choice('buyback.name'    , 1)) !!}</th>
								<th>{!! ucfirst(trans_choice('buyback.group'   , 1)) !!}</th>
								<th>{!! ucfirst(trans_choice('buyback.category', 1)) !!}</th>
								<th>{!! ucfirst(trans_choice('buyback.quantity', 1)) !!}</th>
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
		</div>
	@else
		<div class="col-md-12">

			<div class="panel">
				<div class="panel-heading" style="text-align: center;">
					<h4>{!! trans('buyback.contract_total', ['total' => number_format($buyback->totalValueModded, 2, '.', ',')]) !!}</h4>
				</div>
			</div>

			<div class="panel">
				<div class="panel-body" style="text-align: center;">
					<h5>{!! ucfirst(trans('buyback.acceptable')) !!}</h5>
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
				<div class="panel-body" style="text-align: center;">
					<h5>{!! ucfirst(trans('buyback.breakdown')) !!}</5>
				</div>
				<div class="panel-body">
					<table id="breakdown" class="table table-condensed table-striped table-hover">
						<thead>
							<tr>
								<th>{!! ucfirst(trans_choice('buyback.name'    , 1)) !!}</th>
								<th>{!! ucfirst(trans_choice('buyback.quantity', 1)) !!}</th>
								<th>{!! ucfirst(trans       ('buyback.unit'       )) !!}</th>
								<th>{!! ucfirst(trans       ('buyback.total'      )) !!}</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($buyback->raw as $item)
								<tr>
									<td><img src="https://image.eveonline.com/Type/{!! $item->type->typeID !!}_32.png"> {!! $item->type->typeName !!}</td>
									<td>{!! number_format($item->quantity      , 0, '.', ',') !!}</td>
									<td>{!! number_format($item->buyUnitModded , 2, '.', ',') !!}</td>
									<td>{!! number_format($item->buyTotalModded, 2, '.', ',') !!}</td>
								</tr>
							@endForeach
							@foreach ($buyback->materials as $item)
							@if ($item->quantity > 0)
								<tr>
									<td><img src="https://image.eveonline.com/Type/{!! $item->type->typeID !!}_32.png"> {!! $item->type->typeName !!}</td>
									<td>{!! number_format($item->quantity      , 0, '.', ',') !!}</td>
									<td>{!! number_format($item->buyUnitModded , 2, '.', ',') !!}</td>
									<td>{!! number_format($item->buyTotalModded, 2, '.', ',') !!}</td>
								</tr>
							@endif
							@endForeach
						</tbody>
					</table>
				</div>
			</div>

		</div>
	@endif

</div>
@endsection

@section('scripts')
@parent
<script>
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

	function updateSellTotal() {
		total = 0.0;
		$(".sell-subtotal").each(function(index) {
			subtotal  = parseFloat($(this).html());
			subtotal  = (isNaN(subtotal)) ? 0 : subtotal;
			total    += subtotal;
		});
		$("#sell-total").html(total.toFixed(2));
	};

	$(".sell-control").on("input", function() {
		quantity = parseInt($(this).val());
		quantity = isNaN(quantity) ? 0 : quantity;
		price    = $(this).data("price");

		$("#sell-subtotal-" + $(this).data("typeid")).html((quantity*price).toFixed(2));

		updateSellTotal();
	});

	$(document).ready(function() {
		$('input,textarea').attr('autocomplete', 'off');
		$('[data-toggle="tooltip"]').tooltip();

		$('#wanted'   ).DataTable();
		$('#unwanted' ).DataTable();
		$('#breakdown').DataTable();
	});
</script>
@endsection
