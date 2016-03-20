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
				<b><center>{!! trans('buyback.messages.instructions', ['link' => $ownerLink]) !!}</center></b>
			</div>
		</div>
	</div>

</div>

<div class="col-md-offset-2 col-md-8">

	@if (count($buyback->unwanted) > 0)
		<div class="col-md-12">
			<div class="panel">
				<div class="panel-heading" style="text-align: center;">
					<h5>{!! trans('buyback.headers.unwanted') !!}</h5>
				</div>
				<div class="panel-body">
					<table id="unwanted" class="table table-condensed table-striped table-hover">
						<thead>
							<tr>
								<th>{!! trans       ('buyback.headers.name'         ) !!}</th>
								<th>{!! trans_choice('buyback.headers.groups'    , 1) !!}</th>
								<th>{!! trans_choice('buyback.headers.categories', 1) !!}</th>
								<th>{!! trans_choice('buyback.headers.quantities', 1) !!}</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th>{!! trans       ('buyback.headers.name'         ) !!}</th>
								<th>{!! trans_choice('buyback.headers.groups'    , 1) !!}</th>
								<th>{!! trans_choice('buyback.headers.categories', 1) !!}</th>
								<th>{!! trans_choice('buyback.headers.quantities', 1) !!}</th>
							</tr>
						</tfoot>
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
					<h3>{!! trans('buyback.messages.contract_total', ['total' => number_format($buyback->totalValueModded, 2, '.', ',')]) !!}</h3>
				</div>
			</div>

			<div class="panel">
				<div class="panel-body" style="text-align: center;">
					<h5>{!! ucfirst(trans('buyback.headers.acceptable')) !!}</h5>
				</div>
				<div class="panel-body">
					<table id="wanted" class="table table-condensed table-striped table-hover">
						<thead>
							<tr>
								<th>{!! trans       ('buyback.headers.name'         ) !!}</th>
								<th>{!! trans_choice('buyback.headers.groups'    , 1) !!}</th>
								<th>{!! trans_choice('buyback.headers.categories', 1) !!}</th>
								<th>{!! trans_choice('buyback.headers.quantities', 1) !!}</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th>{!! trans       ('buyback.headers.name'         ) !!}</th>
								<th>{!! trans_choice('buyback.headers.groups'    , 1) !!}</th>
								<th>{!! trans_choice('buyback.headers.categories', 1) !!}</th>
								<th>{!! trans_choice('buyback.headers.quantities', 1) !!}</th>
							</tr>
						</tfoot>
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
					<h5>{!! ucfirst(trans('buyback.headers.breakdown')) !!}</5>
				</div>
				<div class="panel-body">
					<table id="breakdown" class="table table-condensed table-striped table-hover">
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
	<script type="text/javascript">
		$("#wanted"   ).DataTable();
		$("#breakdown").DataTable();
	</script>
@endsection
