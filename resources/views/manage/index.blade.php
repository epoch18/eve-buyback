@extends('layouts.main')

@section('content')
<div class="col-md-offset-1 col-md-10">

	<div class="col-md-12">
		<div class="panel">
			<div class="panel-heading">
				<h5>{!! trans('buyback.headers.motd') !!}</h5>
			</div>
			<div class="panel-body">
				<form id="manage-form-motd" action="{!! route('manage.motd.edit') !!}" method="POST">
					<input type="hidden" name="_token" value="{!! csrf_token() !!}">
					<div class="form-group">
						<textarea id="text" name="text" class="form-control" rows="10">{!! $motd !!}</textarea>
					</div>
					<div class="form-group">
						<button id="submit" class="btn btn-default">{!! trans('buyback.buttons.edit') !!}</button>
					</div>
				</form>
			</div>
			<div class="panel-footer">
				<small>{!! trans('buyback.messages.markdown_enabled') !!}</small>
			</div>
		</div>
	</div>

	<div class="col-md-12">
		<div class="panel">
			<div class="panel-heading">
				<h5>{!! trans('buyback.headers.item_settings') !!}</h5>
			</div>
			<div class="panel-body table-responsive">
				<table id="manage-form-items" class="table table-condensed table-striped table-hover">
					<thead>
						<tr>
							<th></th>
							<th>{!! trans       ('buyback.headers.name'         ) !!}</th>
							<th>{!! trans_choice('buyback.headers.groups'    , 1) !!}</th>
							<th>{!! trans_choice('buyback.headers.categories', 1) !!}</th>
							<th><span class="fa fa-fw fa-cube"     data-toggle="tooltip" data-placement="top"title="{!! trans('buyback.messages.buy_raw'     ) !!}"></span></th>
							<th><span class="fa fa-fw fa-recycle"  data-toggle="tooltip" data-placement="top"title="{!! trans('buyback.messages.buy_recycled') !!}"></span></th>
							<th><span class="fa fa-fw fa-industry" data-toggle="tooltip" data-placement="top"title="{!! trans('buyback.messages.buy_refined' ) !!}"></span></th>
							<th>{!! trans       ('buyback.headers.modifier'     ) !!}</th>
							<th>{!! trans       ('buyback.headers.price'        ) !!}</th>
							<th>{!! trans       ('buyback.headers.sell'         ) !!}</th>
							<th>{!! trans       ('buyback.headers.modifier'     ) !!}</th>
							<th>{!! trans       ('buyback.headers.price'        ) !!}</th>
							<th><span class="fa fa-fw fa-lock" data-toggle="tooltip" data-placement="top"title="{!! trans('buyback.config.items.lock_prices') !!}"></span></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th></th>
							<th>{!! trans       ('buyback.headers.name'         ) !!}</th>
							<th>{!! trans_choice('buyback.headers.groups'    , 1) !!}</th>
							<th>{!! trans_choice('buyback.headers.categories', 1) !!}</th>
							<th><span class="fa fa-fw fa-cube"     data-toggle="tooltip" data-placement="top"title="{!! trans('buyback.messages.buy_raw'     ) !!}"></span></th>
							<th><span class="fa fa-fw fa-recycle"  data-toggle="tooltip" data-placement="top"title="{!! trans('buyback.messages.buy_recycled') !!}"></span></th>
							<th><span class="fa fa-fw fa-industry" data-toggle="tooltip" data-placement="top"title="{!! trans('buyback.messages.buy_refined' ) !!}"></span></th>
							<th>{!! trans       ('buyback.headers.modifier'     ) !!}</th>
							<th>{!! trans       ('buyback.headers.price'        ) !!}</th>
							<th>{!! trans       ('buyback.headers.sell'         ) !!}</th>
							<th>{!! trans       ('buyback.headers.modifier'     ) !!}</th>
							<th>{!! trans       ('buyback.headers.price'        ) !!}</th>
							<th><span class="fa fa-fw fa-lock" data-toggle="tooltip" data-placement="top"title="{!! trans('buyback.messages.lock_prices') !!}"></span></th>
						</tr>
					</tfoot>
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
		initManageMotd({
			actions: {
				'editMotd': "{!! route('manage.motd.edit') !!}",
			},
			token: "{!! csrf_token() !!}",
			trans: {!! $trans !!},
		});

		initManageItems({
			actions: {
				getTypes     : "{!! route('manage.resource.types'     ) !!}",
				getGroups    : "{!! route('manage.resource.groups'    ) !!}",
				getCategories: "{!! route('manage.resource.categories') !!}",

				getItems     : "{!! route('manage.item.get'   ) !!}",
				addItems     : "{!! route('manage.item.add'   ) !!}",
				editItems    : "{!! route('manage.item.edit'  ) !!}",
				removeItems  : "{!! route('manage.item.remove') !!}",
				updateItems  : "{!! route('manage.item.update') !!}",
			},
			token: "{!! csrf_token() !!}",
			trans: {!! $trans !!},
		});
	});
</script>
@endsection
