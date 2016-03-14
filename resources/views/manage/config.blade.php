@extends('layouts.main')

@section('content')
<div class="col-md-offset-1 col-md-10">

	<div class="col-md-12">
		<div class="panel">
			<div class="panel-heading">
				<h5>{!! trans('buyback.config.motd.header') !!}</h5>
			</div>
			<div class="panel-body">
				<form id="manage-form-motd" action="{!! route('config.motd') !!}" method="POST">
					<input type="hidden" name="_token" value="{!! csrf_token() !!}">
					<div class="form-group">
						<textarea id="text" name="text" class="form-control" rows="10">{!! $motd !!}</textarea>
					</div>
					<div class="form-group">
						<button id="submit" class="btn btn-default">{!! ucfirst(trans('buyback.config.motd.submit')) !!}</button>
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
			<div class="panel-body table-responsive">

				<table id="manage-form-items" class="table table-condensed table-striped table-hover">
					<thead>
						<tr>
							<th></th>
							<th>{!! trans       ('buyback.config.items.name'       ) !!}</th>
							<th>{!! trans_choice('buyback.config.items.group'   , 1) !!}</th>
							<th>{!! trans_choice('buyback.config.items.category', 1) !!}</th>
							<th><span class="fa fa-fw fa-cube"     data-toggle="tooltip" data-placement="top"title="{!! trans('buyback.config.items.buy_raw'     ) !!}"></span></th>
							<th><span class="fa fa-fw fa-recycle"  data-toggle="tooltip" data-placement="top"title="{!! trans('buyback.config.items.buy_recycled') !!}"></span></th>
							<th><span class="fa fa-fw fa-industry" data-toggle="tooltip" data-placement="top"title="{!! trans('buyback.config.items.buy_refined' ) !!}"></span></th>
							<th>{!! trans       ('buyback.config.items.modifier'   ) !!}</th>
							<th>{!! trans       ('buyback.config.items.price'      ) !!}</th>
							<th>{!! trans       ('buyback.config.items.sell'       ) !!}</th>
							<th>{!! trans       ('buyback.config.items.modifier'   ) !!}</th>
							<th>{!! trans       ('buyback.config.items.price'      ) !!}</th>
							<th><span class="fa fa-fw fa-lock" data-toggle="tooltip" data-placement="top"title="{!! trans('buyback.config.items.lock_prices') !!}"></span></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th></th>
							<th>{!! trans       ('buyback.config.items.name'       ) !!}</th>
							<th>{!! trans_choice('buyback.config.items.group'   , 1) !!}</th>
							<th>{!! trans_choice('buyback.config.items.category', 1) !!}</th>
							<th><span class="fa fa-fw fa-cube"     data-toggle="tooltip" data-placement="top"title="{!! trans('buyback.config.items.buy_raw'     ) !!}"></span></th>
							<th><span class="fa fa-fw fa-recycle"  data-toggle="tooltip" data-placement="top"title="{!! trans('buyback.config.items.buy_recycled') !!}"></span></th>
							<th><span class="fa fa-fw fa-industry" data-toggle="tooltip" data-placement="top"title="{!! trans('buyback.config.items.buy_refined' ) !!}"></span></th>
							<th>{!! trans       ('buyback.config.items.modifier'   ) !!}</th>
							<th>{!! trans       ('buyback.config.items.price'      ) !!}</th>
							<th>{!! trans       ('buyback.config.items.sell'       ) !!}</th>
							<th>{!! trans       ('buyback.config.items.modifier'   ) !!}</th>
							<th>{!! trans       ('buyback.config.items.price'      ) !!}</th>
							<th><span class="fa fa-fw fa-lock" data-toggle="tooltip" data-placement="top"title="{!! trans('buyback.config.items.lock_prices') !!}"></span></th>
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
		initManageItems({
			actions: {
				addItems     : "{!! route('config.items.add'     ) !!}",
				getItems     : "{!! route('config.items.get'     ) !!}",
				getTypes     : "{!! route('config.types.get'     ) !!}",
				getGroups    : "{!! route('config.groups.get'    ) !!}",
				getCategories: "{!! route('config.categories.get') !!}",
				removeItems  : "{!! route('config.items.remove'  ) !!}",
				updateItems  : "{!! route('config.items.update'  ) !!}",
				updatePrices : "{!! route('config.prices.update' ) !!}",
			},
			token: "{!! csrf_token() !!}",
			trans: {
				buyback: {
					config: {
						items: {
							add                : "{!! trans       ('buyback.config.items.add'                 ) !!}",
							add_items          : "{!! trans       ('buyback.config.items.add_items'           ) !!}",
							buy_modifier       : "{!! trans       ('buyback.config.items.buy_modifier'        ) !!}",
							buy_price          : "{!! trans       ('buyback.config.items.buy_price'           ) !!}",
							buy_raw_help_1     : "{!! trans_choice('buyback.config.items.buy_raw_help'     , 1) !!}",
							buy_raw_help_2     : "{!! trans_choice('buyback.config.items.buy_raw_help'     , 2) !!}",
							buy_recycled_help_1: "{!! trans_choice('buyback.config.items.buy_recycled_help', 1) !!}",
							buy_recycled_help_2: "{!! trans_choice('buyback.config.items.buy_recycled_help', 2) !!}",
							buy_refined_help_1 : "{!! trans_choice('buyback.config.items.buy_refined_help' , 1) !!}",
							buy_refined_help_2 : "{!! trans_choice('buyback.config.items.buy_refined_help' , 2) !!}",
							buy_settings       : "{!! trans       ('buyback.config.items.buy_settings'        ) !!}",
							cancel             : "{!! trans       ('buyback.config.items.cancel'              ) !!}",
							category_1         : "{!! trans_choice('buyback.config.items.category'         , 1) !!}",
							category_2         : "{!! trans_choice('buyback.config.items.category'         , 2) !!}",
							confirm_remove_1   : "{!! trans_choice('buyback.config.items.confirm_remove'   , 1) !!}",
							confirm_remove_2   : "{!! trans_choice('buyback.config.items.confirm_remove'   , 2) !!}",
							edit               : "{!! trans       ('buyback.config.items.edit'                ) !!}",
							group_1            : "{!! trans_choice('buyback.config.items.group'            , 1) !!}",
							group_2            : "{!! trans_choice('buyback.config.items.group'            , 2) !!}",
							item_1             : "{!! trans_choice('buyback.config.items.item'             , 1) !!}",
							item_2             : "{!! trans_choice('buyback.config.items.item'             , 2) !!}",
							item_settings      : "{!! trans       ('buyback.config.items.item_settings'       ) !!}",
							lock_prices_help   : "{!! trans       ('buyback.config.items.lock_prices_help'    ) !!}",
							remove_item_1      : "{!! trans_choice('buyback.config.items.remove_item'      , 1) !!}",
							remove_item_2      : "{!! trans_choice('buyback.config.items.remove_item'      , 2) !!}",
							remove             : "{!! trans       ('buyback.config.items.remove'              ) !!}",
							sell_help_1        : "{!! trans_choice('buyback.config.items.sell_help'        , 1) !!}",
							sell_help_2        : "{!! trans_choice('buyback.config.items.sell_help'        , 2) !!}",
							sell_modifier      : "{!! trans       ('buyback.config.items.sell_modifier'       ) !!}",
							sell_price         : "{!! trans       ('buyback.config.items.sell_price'          ) !!}",
							sell_settings      : "{!! trans       ('buyback.config.items.sell_settings'       ) !!}",
							update             : "{!! trans       ('buyback.config.items.update'              ) !!}",
							update_item_1      : "{!! trans_choice('buyback.config.items.update_item'      , 1) !!}",
							update_item_2      : "{!! trans_choice('buyback.config.items.update_item'      , 2) !!}",
							update_prices      : "{!! trans       ('buyback.config.items.update_prices'       ) !!}",
						},
					},
				},
			},
		});
	});
</script>
@endsection
