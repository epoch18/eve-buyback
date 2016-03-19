@extends('layouts.main')

@section('content')
<div class="col-md-offset-1 col-md-10 table-responsive">
	<table id="buying" class="table table-condensed table-striped table-hover">
		<caption><h4>{!! trans('buyback.headers.buying') !!}</h4></caption>
		<thead>
			<tr>
				<th>{!! trans('buyback.headers.issued'    ) !!}</th>
				<th>{!! trans('buyback.headers.issuer'    ) !!}</th>
				<th>{!! trans('buyback.headers.location'  ) !!}</th>
				<th>{!! trans('buyback.headers.volume'    ) !!}</th>
				<th>{!! trans('buyback.headers.asking'    ) !!}</th>
				<th>{!! trans('buyback.headers.calculated') !!}</th>
				<th>{!! trans('buyback.headers.market'    ) !!}</th>
				<th>{!! trans('buyback.headers.title'     ) !!}</th>
				<th>{!! trans('buyback.headers.margin'    ) !!}</th>
				<th></th>
				<th></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th>{!! trans('buyback.headers.issued'    ) !!}</th>
				<th>{!! trans('buyback.headers.issuer'    ) !!}</th>
				<th>{!! trans('buyback.headers.location'  ) !!}</th>
				<th>{!! trans('buyback.headers.volume'    ) !!}</th>
				<th>{!! trans('buyback.headers.asking'    ) !!}</th>
				<th>{!! trans('buyback.headers.calculated') !!}</th>
				<th>{!! trans('buyback.headers.market'    ) !!}</th>
				<th>{!! trans('buyback.headers.title'     ) !!}</th>
				<th>{!! trans('buyback.headers.margin'    ) !!}</th>
				<th></th>
				<th></th>
			</tr>
		</tfoot>
		<tbody>
			@foreach ($buying as $contract)
				@if (count($contract->unwanted) > 0)
					<tr class="danger  accordion-toggle">
				@elseif ($contract->totalMargin > 7.50)
					<tr class="success accordion-toggle">
				@elseif ($contract->totalMargin > 2.50)
					<tr class="warning accordion-toggle">
				@else
					<tr class="danger  accordion-toggle">
				@endif
					<td>{!! $contract->contract->dateIssued !!}</td>
					<td><a href="#" onclick="CCPEVE.showInfo(1377, {!! $contract->contract->issuerID         !!})">{!! $contract->contractIssuer !!}</a></td>
					<td><a href="#" onclick="CCPEVE.showInfo(3867, {!! $contract->contractStation->stationID !!})">{!! str_limit($contract->contractStation->stationName, 42) !!}</a></td>
					<td>{!! number_abbreviate($contract->contract->volume             ) !!} </td>
					<td>{!! number_abbreviate($contract->contractPrice                ) !!} </td>
					<td>{!! number_abbreviate($contract->totalValueModded             ) !!} </td>
					<td>{!! number_abbreviate($contract->totalValue                   ) !!} </td>
					<td>{!!                  ($contract->contract->title              ) !!} </td>
					<td>{!! number_format    ($contract->totalMargin     , 2, '.', ',') !!}%</td>
					<td><a href="#" onclick="CCPEVE.showContract({!! $contract->contractStation->solarSystem->solarSystemID !!} , {!! $contract->contract->contractID !!})">{!! trans_choice('buyback.headers.contracts', 1) !!}</a></td>
					<td><a href="#" onClick="collapse('#contract-{!! $contract->contract->contractID !!}');">{!! trans('buyback.headers.details') !!}</a></td>
				</tr>
				<tr>
					<td colspan="11" class="hiddenRow hidden" id="contract-{!! $contract->contract->contractID !!}-td">
						<div class="table-responsive accordian-body collapse" id="contract-{!! $contract->contract->contractID !!}">

							<div class="col-md-5">
								<table id="" class="table table-condensed table-striped table-hover">
									<caption>{!! trans('buyback.headers.unwanted') !!}</caption>
									<thead>
										<tr>
											<th>{!! trans       ('buyback.headers.name'         ) !!}</th>
											<th>{!! trans_choice('buyback.headers.quantities', 1) !!}</th>
										</tr>
									</thead>
									<tfoot>
										<tr>
											<th>{!! trans       ('buyback.headers.name'         ) !!}</th>
											<th>{!! trans_choice('buyback.headers.quantities', 1) !!}</th>
										</tr>
									</tfoot>
									<tbody>
										@foreach ($contract->unwanted as $item)
											<tr>
												<td><img src="https://image.eveonline.com/Type/{!! $item->type->typeID !!}_32.png"> {!! $item->type->typeName !!}</td>
												<td>{!! number_format($item->quantity, 0, '.', ',') !!}</td>
											</tr>
										@endForeach
									</tbody>
								</table>
							</div>

							<div class="col-md-7">
								<table id="" class="table table-condensed table-striped table-hover">
									<caption>{!! trans('buyback.headers.wanted') !!}</caption>
									<thead>
										<tr>
											<th>{!! trans       ('buyback.headers.name'         ) !!}</th>
											<th>{!! trans_choice('buyback.headers.quantities', 1) !!}</th>
											<th>{!! trans       ('buyback.headers.price'        ) !!}</th>
											<th>{!! trans       ('buyback.headers.total'        ) !!}</th>
										</tr>
									</thead>
									<tfoot>
										<tr>
											<th>{!! trans       ('buyback.headers.name'         ) !!}</th>
											<th>{!! trans_choice('buyback.headers.quantities', 1) !!}</th>
											<th>{!! trans       ('buyback.headers.price'        ) !!}</th>
											<th>{!! trans       ('buyback.headers.total'        ) !!}</th>
										</tr>
									</tfoot>
									<tbody>
										@foreach ($contract->raw as $item)
											<tr>
												<td><img src="https://image.eveonline.com/Type/{!! $item->type->typeID !!}_32.png"> {!! $item->type->typeName !!}</td>
												<td>{!! number_format($item->quantity, 0) !!}</td>
												<td>{!! number_format($item->buyUnit , 2) !!}</td>
												<td>{!! number_format($item->buyTotal, 2) !!}</td>
											</tr>
										@endForeach
									</tbody>
								</table>
							</div>

							<div class="col-md-5">
								<table id="" class="table table-condensed table-striped table-hover">
									<caption>{!! trans('buyback.headers.reprocessable') !!}</caption>
									<thead>
										<tr>
											<th>{!! trans       ('buyback.headers.name'         ) !!}</th>
											<th>{!! trans_choice('buyback.headers.quantities', 1) !!}</th>
										</tr>
									</thead>
									<tfoot>
										<tr>
											<th>{!! trans       ('buyback.headers.name'         ) !!}</th>
											<th>{!! trans_choice('buyback.headers.quantities', 1) !!}</th>
										</tr>
									</tfoot>
									<tbody>
										@foreach ($contract->recycled as $item)
											<tr>
												<td><img src="https://image.eveonline.com/Type/{!! $item->type->typeID !!}_32.png"> {!! $item->type->typeName !!}</td>
												<td>{!! number_format($item->quantity, 0, '.', ',') !!}</td>
											</tr>
										@endForeach
										@foreach ($contract->refined as $item)
											<tr>
												<td><img src="https://image.eveonline.com/Type/{!! $item->type->typeID !!}_32.png"> {!! $item->type->typeName !!}</td>
												<td>{!! number_format($item->quantity, 0, '.', ',') !!}</td>
											</tr>
										@endForeach
									</tbody>
								</table>
							</div>

							<div class="col-md-7">
								<table id="" class="table table-condensed table-striped table-hover">
									<caption>{!! trans('buyback.headers.materials') !!}</caption>
									<thead>
										<tr>
											<th>{!! trans       ('buyback.headers.name'         ) !!}</th>
											<th>{!! trans_choice('buyback.headers.quantities', 1) !!}</th>
											<th>{!! trans       ('buyback.headers.price'        ) !!}</th>
											<th>{!! trans       ('buyback.headers.total'        ) !!}</th>
										</tr>
									</thead>
									<tfoot>
										<tr>
											<th>{!! trans       ('buyback.headers.name'         ) !!}</th>
											<th>{!! trans_choice('buyback.headers.quantities', 1) !!}</th>
											<th>{!! trans       ('buyback.headers.price'        ) !!}</th>
											<th>{!! trans       ('buyback.headers.total'        ) !!}</th>
										</tr>
									</tfoot>
									<tbody>
										@foreach ($contract->materials as $item)
											@if ($item->quantity > 0)
												<tr>
													<td><img src="https://image.eveonline.com/Type/{!! $item->type->typeID !!}_32.png"> {!! $item->type->typeName !!}</td>
													<td>{!! number_format($item->quantity, 0) !!}</td>
													<td>{!! number_format($item->buyUnit , 2) !!}</td>
													<td>{!! number_format($item->buyTotal, 2) !!}</td>
												</tr>
											@endif
										@endForeach
									</tbody>
								</table>
							</div>

						</div>
					</td>
				</tr>
			@endForeach
		</tbody>
	</table>
</div>
@endSection

@section('scripts')
@parent
<script>
	function collapse(id) {
		var visible = $(id+"-td").is(":visible");

		if (visible == true) {
			$(id+"-td").addClass('hidden');
			$(id).collapse("hide");

		 } else {
			$(id+"-td").removeClass('hidden');
			$(id).collapse("show");
		}
	}
</script>
@endSection
