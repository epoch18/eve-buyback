@extends('layouts.main')

@section('content')

<div class="col-md-offset-1 col-md-10 table-responsive">
	<table id="buying" class="table table-condensed table-striped table-hover">
		<caption><h4>{!! ucfirst(trans('buyback.buying')) !!}</h4></caption>
		<thead>
			<tr>
				<th>{!! ucfirst(trans('buyback.issued'    )) !!}</th>
				<th>{!! ucfirst(trans('buyback.issuer'    )) !!}</th>
				<th>{!! ucfirst(trans('buyback.location'  )) !!}</th>
				<th>{!! ucfirst(trans('buyback.volume'    )) !!}</th>
				<th>{!! ucfirst(trans('buyback.asking'    )) !!}</th>
				<th>{!! ucfirst(trans('buyback.calculated')) !!}</th>
				<th>{!! ucfirst(trans('buyback.market'    )) !!}</th>
				<th>{!! ucfirst(trans('buyback.title'     )) !!}</th>
				<th>{!! ucfirst(trans('buyback.margin'    )) !!}</th>
				<th></th>
				<th></th>
			</tr>
		</thead>
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
					<td><a href="#" onclick="CCPEVE.showContract({!! $contract->contractStation->solarSystem->solarSystemID !!} , {!! $contract->contract->contractID !!})">{!! ucfirst(trans_choice('buyback.contract', 1)) !!}</a></td>
					<td><a href="#" onClick="collapse('#contract-{!! $contract->contract->contractID !!}');">{!! ucfirst(trans_choice('buyback.detail', 2)) !!}</a></td>
				</tr>
				<tr>
					<td colspan="11" class="hiddenRow hidden" id="contract-{!! $contract->contract->contractID !!}-td">
						<div class="table-responsive accordian-body collapse" id="contract-{!! $contract->contract->contractID !!}">

							<div class="col-md-5">
								<table id="" class="table table-condensed table-striped table-hover">
									<caption>{!! ucfirst(trans('buyback.unwanted')) !!}</caption>
									<thead>
										<tr>
											<th>{!! ucfirst(trans_choice('buyback.name'    , 1)) !!}</th>
											<th>{!! ucfirst(trans_choice('buyback.quantity', 1)) !!}</th>
										</tr>
									</thead>
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
									<caption>{!! ucfirst(trans('buyback.wanted')) !!}</caption>
									<thead>
										<tr>
											<th>{!! ucfirst(trans_choice('buyback.name'    , 1)) !!}</th>
											<th>{!! ucfirst(trans_choice('buyback.quantity', 1)) !!}</th>
											<th>{!! ucfirst(trans       ('buyback.unit'       )) !!}</th>
											<th>{!! ucfirst(trans       ('buyback.total'      )) !!}</th>
										</tr>
									</thead>
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
									<caption>{!! ucfirst(trans('buyback.reprocessable')) !!}</caption>
									<thead>
										<tr>
											<th>{!! ucfirst(trans_choice('buyback.name'    , 1)) !!}</th>
											<th>{!! ucfirst(trans_choice('buyback.quantity', 1)) !!}</th>
										</tr>
									</thead>
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
									<caption>{!! ucfirst(trans_choice('buyback.material', 2)) !!}</caption>
									<thead>
										<tr>
											<th>{!! ucfirst(trans_choice('buyback.name'    , 1)) !!}</th>
											<th>{!! ucfirst(trans_choice('buyback.quantity', 1)) !!}</th>
											<th>{!! ucfirst(trans       ('buyback.unit'       )) !!}</th>
											<th>{!! ucfirst(trans       ('buyback.total'      )) !!}</th>
										</tr>
									</thead>
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
			$(id).collapse("hide"); }
		else {
			$(id+"-td").removeClass('hidden');
			$(id).collapse("show"); }
	}

	$(document).ready(function() {
		//$('#buying').DataTable();
	});
</script>

@endSection
