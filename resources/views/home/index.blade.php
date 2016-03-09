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

	@if ($motd)
	<div class="col-md-12">
		<div class="panel">
			<div class="panel-body">
				<p>{!! $motd !!}</p>
			</div>
		</div>
	</div>
	@endif


	<div class="col-md-12">
		<div class="panel">
			<div class="panel-heading">
				<h5>{!! ucfirst(trans('buyback.buying')) !!}</h5>
			</div>
			<div class="panel-body">
				<table id="buying" class="table table-condensed table-striped table-hover">
					<thead>
						<tr>
						<tr>
							<th>{!! ucfirst(trans_choice('buyback.name'    , 1)) !!}</th>
							<th>{!! ucfirst(trans_choice('buyback.group'   , 1)) !!}</th>
							<th>{!! ucfirst(trans_choice('buyback.category', 1)) !!}</th>
							<th>{!! ucfirst(trans       ('buyback.unit'       )) !!}</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($buying as $item)
							<tr>
								<td>
									<img src="https://image.eveonline.com/Type/{!! $item->type->typeID !!}_32.png">
									{!! $item->type->typeName !!}
									<span style="float: right;">
									@if ($item->buyRaw     ) <span class="fa fa-fw fa-cube"     data-toggle="tooltip" data-placement="top"title="{!! trans('buyback.buying_raw'     ) !!}"></span> @endif
									@if ($item->buyRecycled) <span class="fa fa-fw fa-recycle"  data-toggle="tooltip" data-placement="top"title="{!! trans('buyback.buying_recycled') !!}"></span> @endif
									@if ($item->buyRefined ) <span class="fa fa-fw fa-industry" data-toggle="tooltip" data-placement="top"title="{!! trans('buyback.buying_refined' ) !!}"></span> @endif
									</span>
								</td>
								<td>{!! $item->type->group->groupName              !!}</td>
								<td>{!! $item->type->group->category->categoryName !!}</td>
								<td>{!! number_format($item->buyPrice * $item->buyModifier, 2, '.', ',') !!}</td>
							</tr>
						@endForeach
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="col-md-12">
		<div class="panel">
			<div class="panel-heading">
				<h5>{!! ucfirst(trans('buyback.selling')) !!}</h5>
			</div>
			<div class="panel-body">
				<table id="selling" class="table table-condensed table-striped table-hover">
					<thead>
						<tr>
						<tr>
							<th>{!! ucfirst(trans_choice('buyback.name'    , 1)) !!}</th>
							<th>{!! ucfirst(trans_choice('buyback.group'   , 1)) !!}</th>
							<th>{!! ucfirst(trans_choice('buyback.category', 1)) !!}</th>
							<th>{!! ucfirst(trans       ('buyback.unit'       )) !!}</th>
							<th>{!! ucfirst(trans_choice('buyback.quantity', 1)) !!}</th>
							<th>{!! ucfirst(trans_choice('buyback.subtotal', 1)) !!}</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($selling as $item)
							<tr>
								<td><img src="https://image.eveonline.com/Type/{!! $item->type->typeID !!}_32.png"> {!! $item->type->typeName !!}</td>
								<td>{!! $item->type->group->groupName              !!}</td>
								<td>{!! $item->type->group->category->categoryName !!}</td>
								<td>{!! number_format($item->sellPrice * $item->sellModifier, 2, '.', ',') !!}</td>
								<td><input type="number" class="form-control sell-control" min="0" data-typeid="{!! $item->type->typeID !!}" data-price="{!! $item->sellPrice * $item->sellModifier !!}"></td>
								<td class="sell-subtotal" id="sell-subtotal-{!! $item->type->typeID !!}">0.00</td>
							</tr>
						@endForeach
					</tbody>
				</table>
			</div>
			<div class="panel-footer">
				<h5 style="text-align: right;">{!! trans('buyback.contract_total', ['total' => '<span class="sell-total" id="sell-total">0.00</span>']) !!}</h5>
			</div>
		</div>
	</div>

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

		$('#buying'   ).DataTable();
		$('#selling'  ).DataTable();
	});
</script>
@endsection
