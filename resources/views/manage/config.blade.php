@extends('layouts.main')

@section('styles')
@parent
<style type="text/css">
	.dataTable > thead > tr > th[class*="sort"]:after{
		content: "" !important;
	}
	input[type="number"] {
		width: 125px;
	}
	input {
		max-width: 100%;
	}
</style>
@endsection

@section('content')
<div class="col-md-offset-1 col-md-10">

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

	<div class="col-md-12">
		<div class="panel">
			<div class="panel-heading">
				<h5>{!! trans('buyback.config.items.header') !!}</h5>
			</div>
			<div class="panel-body">

				<table id="items" class="table table-condensed table-striped table-hover">
					<thead>
						<tr>
							<th></th>
							<th>{!! trans('buyback.config.items.name'    ) !!}</th>
							<th>{!! trans('buyback.config.items.group'   ) !!}</th>
							<th>{!! trans('buyback.config.items.category') !!}</th>
							<th><span class="fa fa-fw fa-cube"     data-toggle="tooltip" data-placement="top"title="{!! trans('buyback.config.items.buy_raw'     ) !!}"></span></th>
							<th><span class="fa fa-fw fa-recycle"  data-toggle="tooltip" data-placement="top"title="{!! trans('buyback.config.items.buy_recycled') !!}"></span></th>
							<th><span class="fa fa-fw fa-industry" data-toggle="tooltip" data-placement="top"title="{!! trans('buyback.config.items.buy_refined' ) !!}"></span></th>
							<th>{!! trans('buyback.config.items.modifier') !!}</th>
							<th>{!! trans('buyback.config.items.price'   ) !!}</th>
							<th>{!! trans('buyback.config.items.sell'    ) !!}</th>
							<th>{!! trans('buyback.config.items.modifier') !!}</th>
							<th>{!! trans('buyback.config.items.price'   ) !!}</th>
							<th><span class="fa fa-fw fa-lock" data-toggle="tooltip" data-placement="top"title="{!! trans('buyback.config.items.lock_prices') !!}"></span></th>
						</tr>
					</thead>
				</table>

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

	$('form[name=items]').submit(function () {
		var button = $('form[name=items] #submit');
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

	$(document).ready(function() {
		var items = $('#items').DataTable({
			dom: "lBfrtip",
			ajax: "{!! route('config.items.get') !!}",
			sAjaxDataProp: "",
			columns: [
				{
					data: null,
					defaultContent: '',
					className: 'select-checkbox',
					orderable: false,
				},
				{
					data: "type.typeName",
					render: function (data, type, row, meta) {
						if (type != "display") { return data; }

						return '<img src="https://image.eveonline.com/Type/'+row.typeID+'_32.png"> '+data;
					},
				},
				{
					data: "type.group.groupName" },
				{
					data: "type.group.category.categoryName" },
				{
					data: "buyRaw",
					render: function (data, type, row, meta) {
						if (type != "display") { return data; }

						return data == true
							? '<span class="fa fa-fw fa-check-square-o"></span>'
							: '<span class="fa fa-fw fa-square-o"></span>';
					},
				},
				{
					data: "buyRecycled",
					render: function (data, type, row, meta) {
						if (type != "display") { return data; }

						return data == true
							? '<span class="fa fa-fw fa-check-square-o"></span>'
							: '<span class="fa fa-fw fa-square-o"></span>';
					},
				},
				{
					data: "buyRefined",
					render: function (data, type, row, meta) {
						if (type != "display") { return data; }

						return data == true
							? '<span class="fa fa-fw fa-check-square-o"></span>'
							: '<span class="fa fa-fw fa-square-o"></span>';
					},
				},
				{
					data: "buyModifier",
					render: function (data, type, row, meta) {
						if (type != "display") { return data; }

						return data.toFixed(2);
					},
				},
				{
					data: "buyPrice",
					render: function (data, type, row, meta) {
						if (type != "display") { return data; }

						return data.toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")+' ISK';
					},
				},
				{
					data: "sell",
					render: function (data, type, row, meta) {
						if (type != "display") { return data; }

						return data == true
							? '<span class="fa fa-fw fa-check-square-o"></span>'
							: '<span class="fa fa-fw fa-square-o"></span>';
					},
				},
				{
					data: "sellModifier",
					render: function (data, type, row, meta) {
						if (type != "display") { return data; }

						return data.toFixed(2);
					},
				},
				{
					data: "sellPrice",
					render: function (data, type, row, meta) {
						if (type != "display") { return data; }

						return data.toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")+' ISK';
					},
				},
				{
					data: "lockPrices",
					render: function (data, type, row, meta) {
						if (type != "display") { return data; }

						return data == true
							? '<span class="fa fa-fw fa-check-square-o"></span>'
							: '<span class="fa fa-fw fa-square-o"></span>';
					},
				},
			],
			select: {
				style:    'os',
				selector: 'td:first-child',
			},
			buttons: [
				"selectAll",
				"selectNone",
				{
					text: "{!! trans('buyback.config.items.add') !!}",
					action: function (e, dt, node, config) {
						dt.ajax.reload();
					},
					enabled: true,
				},
				{
					text: "{!! trans('buyback.config.items.edit') !!}",
					action: function (e, dt, node, config) {
						dt.ajax.reload();
					},
					enabled: false,
				},
				{
					text: "{!! trans('buyback.config.items.remove') !!}",
					action: function (e, dt, node, config) {
						dt.ajax.reload();
					},
					enabled: false,
				},
				{
					text: "{!! trans('buyback.config.items.update_prices') !!}",
					action: function (e, dt, node, config) {
						dt.ajax.reload();
					},
					enabled: true,
				},
			],
		});

		$(".dt-buttons").addClass('btn-group');
		$(".dt-button").addClass('btn btn-default');
		$(".dt-button").removeClass('dt-button');

		items.on("select", function (e, dt, type, indexes) {
			var selectedRows = items.rows({selected: true}).count();

			items.button(3).enable(selectedRows == 1);
			items.button(4).enable(selectedRows >= 1);
		});

		items.on("deselect", function (e, dt, type, indexes) {
			var selectedRows = items.rows({selected: true}).count();

			items.button(3).enable(selectedRows == 1);
			items.button(4).enable(selectedRows >= 1);
		});

		items.on("draw", function (e, settings) {
			var selectedRows = items.rows({selected: true}).count();

			items.button(3).enable(selectedRows == 1);
			items.button(4).enable(selectedRows >= 1);
		});

	});
</script>
@endsection
