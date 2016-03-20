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
			<div class="panel-body" style="text-align: center;">
				<b>{!! trans('buyback.messages.instructions', ['link' => $ownerLink]) !!}</b>
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
			<div class="panel-heading" style="text-align: center;">
				<h5>{!! trans('buyback.headers.buying') !!}</h5>
			</div>
			<div class="panel-body">
				<table id="buying" class="table table-condensed table-striped table-hover">
					<thead>
						<tr>
							<th>{!! trans       ('buyback.headers.name'         ) !!}</th>
							<th>{!! trans_choice('buyback.headers.groups'    , 1) !!}</th>
							<th>{!! trans_choice('buyback.headers.categories', 1) !!}</th>
							<th><span class="fa fa-fw fa-cube"     data-toggle="tooltip" data-placement="top" title="{!! trans_choice('buyback.messages.buy_raw'     , 1) !!}"></span></th>
							<th><span class="fa fa-fw fa-recycle"  data-toggle="tooltip" data-placement="top" title="{!! trans_choice('buyback.messages.buy_recycled', 1) !!}"></span></th>
							<th><span class="fa fa-fw fa-industry" data-toggle="tooltip" data-placement="top" title="{!! trans_choice('buyback.messages.buy_refined' , 1) !!}"></span></th>
							<th>{!! trans       ('buyback.headers.price'        ) !!}</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th>{!! trans       ('buyback.headers.name'         ) !!}</th>
							<th>{!! trans_choice('buyback.headers.groups'    , 1) !!}</th>
							<th>{!! trans_choice('buyback.headers.categories', 1) !!}</th>
							<th><span class="fa fa-fw fa-cube"     data-toggle="tooltip" data-placement="top" title="{!! trans_choice('buyback.messages.buy_raw'     , 1) !!}"></span></th>
							<th><span class="fa fa-fw fa-recycle"  data-toggle="tooltip" data-placement="top" title="{!! trans_choice('buyback.messages.buy_recycled', 1) !!}"></span></th>
							<th><span class="fa fa-fw fa-industry" data-toggle="tooltip" data-placement="top" title="{!! trans_choice('buyback.messages.buy_refined' , 1) !!}"></span></th>
							<th>{!! trans       ('buyback.headers.price'        ) !!}</th>
						</tr>
					</tfoot>
					<tbody>
						@foreach ($buying as $item)
							<tr>
								<td>
									<img src="https://image.eveonline.com/Type/{!! $item->type->typeID !!}_32.png">
									{!! $item->type->typeName !!}
								</td>
								<td>{!! $item->type->group->groupName              !!}</td>
								<td>{!! $item->type->group->category->categoryName !!}</td>
								<td>@if ($item->buyRaw     )<span class="fa fa-fw fa-check-square-o"></span>@else<span class="fa fa-fw fa-square-o"></span>@endif</td>
								<td>@if ($item->buyRecycled)<span class="fa fa-fw fa-check-square-o"></span>@else<span class="fa fa-fw fa-square-o"></span>@endif</td>
								<td>@if ($item->buyRefined )<span class="fa fa-fw fa-check-square-o"></span>@else<span class="fa fa-fw fa-square-o"></span>@endif</td>
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
			<div class="panel-heading" style="text-align: center;">
				<h5>{!! trans('buyback.headers.selling') !!}</h5>
			</div>
			<div class="panel-body">
				<table id="selling" class="table table-condensed table-striped table-hover">
					<thead>
						<tr>
							<th>{!! trans       ('buyback.headers.name'         ) !!}</th>
							<th>{!! trans_choice('buyback.headers.groups'    , 1) !!}</th>
							<th>{!! trans_choice('buyback.headers.categories', 1) !!}</th>
							<th>{!! trans       ('buyback.headers.price'        ) !!}</th>
							<th>{!! trans_choice('buyback.headers.quantities', 1) !!}</th>
							<th>{!! trans       ('buyback.headers.total'        ) !!}</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th>{!! trans       ('buyback.headers.name'         ) !!}</th>
							<th>{!! trans_choice('buyback.headers.groups'    , 1) !!}</th>
							<th>{!! trans_choice('buyback.headers.categories', 1) !!}</th>
							<th>{!! trans       ('buyback.headers.price'        ) !!}</th>
							<th>{!! trans_choice('buyback.headers.quantities', 1) !!}</th>
							<th>{!! trans       ('buyback.headers.total'        ) !!}</th>
						</tr>
					</tfoot>
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
				<h5 style="text-align: right;">{!! trans('buyback.messages.contract_total', ['total' => '<span class="sell-total" id="sell-total">0.00</span>']) !!}</h5>
			</div>
		</div>
	</div>

</div>

@endsection

@section('scripts')
@parent
<script type="text/javascript">
function updateSellTotal() {
	total = 0.0;
	$(".sell-subtotal").each(function(index) {
		subtotal = parseFloat($(this).html());
		if (isNaN(subtotal)) subtotal = 0;
		total += subtotal;
	});
	$("#leftNet").html(total.toFixed(2));
}

$(".sell-control").on("input",function() {
	quantity = parseInt($(this).val());
	if (isNaN(quantity)) quantity = 0;
	price = $(this).data("price");
	$("#sell-subtotal" + $(this).data("typeid")).html((quantity*price).toFixed(2));
	updateSellTotal();
});

	/*function updateSellTotal() {
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
	});*/

	$(document).ready(function() {
		$('input,textarea').attr('autocomplete', 'off');
		$('[data-toggle="tooltip"]').tooltip();

		$('#buying'   ).DataTable();
		$('#selling'  ).DataTable();
	});
</script>
@endsection
