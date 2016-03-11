function initManageItems(args) {
	var table = $('#manage-form-items').DataTable({
		dom: "lBfrtip",
		ajax: args.actions.getItems,
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
				data: "type.group.groupName",
			},
			{
				data: "type.group.category.categoryName",
			},
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
			style:    "multi",
			selector: "td:first-child",
		},
		buttons: [
			"selectAll",
			"selectNone",
			{
				text: args.trans.buyback.config.items.add,
				action: function (e, dt, node, config) {
					var form = ''
						+ '<div class="row">'
						+ '	<div class="col-md-12">'
						+ '		<form id="manage-form-add-items" class="form-horizontal" action="'+args.actions.addItems+'" method="POST">'
						+ '			<input type="hidden" name="_token" value="'+args.token+'">'
						+ '			<div class="form-group">'
						+ '				<label class="col-md-4 control-label">'+args.trans.buyback.config.items.items+'</label>'
						+ '				<div class="col-md-8">'
						+ '					<select id="types" name="types[]" class="form-control" multiple="multiple"></select>'
						+ '				</div>'
						+ '			</div>'
						+ '		</form>'
						+ '	</div>'
						+ '</div>'
					;

					var box = bootbox.dialog({
						message: form,
						title: args.trans.buyback.config.items.add_items,
						buttons: {
							cancel: {
								label: args.trans.buyback.config.items.cancel,
								className: "btn-default",
								callback: function() {
								},
							},
							update: {
								label: args.trans.buyback.config.items.add,
								className: "btn-success",
								callback: function() {
									var form   = $("#manage-form-add-items");
									var action = form.attr("action");

									$.post(action, form.serialize(), function(response) {
										if (response.result == true) {
											$.notify(response.message, {className: 'success'});
											dt.ajax.reload();
										} else {
											$.notify(response.message, {className: 'error'});
										}
									});
								},
							},
						},
					});

					box.bind('shown.bs.modal', function () {
						$("#manage-form-add-items #types").select2({
							ajax: {
								url: args.actions.getTypes,
								dataType: "json",
								delay: 250,
								minimumInputLength: 3,
								data: function (params) {
									return {
										query: params.term,
										page : params.page,
									};
								},
								processResults: function (data, params) {
									params.page = params.page || 1;

									$.map(data.data, function (obj) {
										obj.id   = obj.typeID;
										obj.text = obj.typeName;
									});

									return {
										results: data.data,
										pagination: {
											more: (params.page * 20) < data.total
										},
									};
								},
								cache: true,
							},
							templateResult: function (state) {
								if (!state.id) { return state.text; }

								return $('<span><img src="https://image.eveonline.com/Type/'+state.typeID+'_32.png"> '+state.typeName+'</span>');
							},
						});
					});

					dt.ajax.reload();
				},
				enabled: true,
			},
			{
				text: args.trans.buyback.config.items.edit,
				action: function (e, dt, node, config) {
					var data  = table.rows('.selected').data();
					var types = "";

					$.each(data, function (index, item) {
						types += item.typeID + ",";
					}); types  = types.substr(0, types.length - 1);

					var title = data.count() == 1
						? args.trans.buyback.config.items.update_item_1
						: args.trans.buyback.config.items.update_item_2;

					var form = ''
						+ '<div class="row">'
						+ '	<div class="col-md-12">'
						+ '		<form id="manage-form-update-items" class="form-horizontal" action="'+args.actions.updateItems+'" method="POST">'
						+ '			<input type="hidden" name="_token" value="'+args.token+'">'
						+ '			<input type="hidden" name="items" value="'+(types)+'">'
						+ '			<div class="form-group">'
						+ '				<label class="col-md-4 control-label">'+args.trans.buyback.config.items.buy_settings+'</label>'
						+ '				<div class="col-md-8">'
						+ '					<div class="checkbox"><label><input type="checkbox" name="buyRaw"     '+(data[0].buyRaw      ? ' checked' : '')+'>'+(data.count() == 1 ? args.trans.buyback.config.items.buy_raw_help_1      : args.trans.buyback.config.items.buy_raw_help_2     )+'</label></div>'
						+ '					<div class="checkbox"><label><input type="checkbox" name="buyRecycled"'+(data[0].buyRecycled ? ' checked' : '')+'>'+(data.count() == 1 ? args.trans.buyback.config.items.buy_recycled_help_1 : args.trans.buyback.config.items.buy_recycled_help_2)+'</label></div>'
						+ '					<div class="checkbox"><label><input type="checkbox" name="buyRefined" '+(data[0].buyRefined  ? ' checked' : '')+'>'+(data.count() == 1 ? args.trans.buyback.config.items.buy_refined_help_1  : args.trans.buyback.config.items.buy_refined_help_2 )+'</label></div>'
						+ '				</div>'
						+ '			</div>'
						+ '			<div class="form-group">'
						+ '				<label class="col-md-4 control-label">'+args.trans.buyback.config.items.buy_modifier+'</label>'
						+ '				<div class="col-md-8">'
						+ '					<input class="form-control" type="number" name="buyModifier" min="0" step="0.01" value="'+(data[0].buyModifier)+'">'
						+ '				</div>'
						+ '			</div>'
						+ (data.count() == 1 ?
						  '			<div class="form-group">'
						+ '				<label class="col-md-4 control-label">'+args.trans.buyback.config.items.buy_price+'</label>'
						+ '				<div class="col-md-8">'
						+ '					<input class="form-control" type="number" name="buyPrice" min="0" value="'+(data[0].buyPrice)+'">'
						+ '				</div>'
						+ '			</div>' : '')
						+ '			<div class="form-group">'
						+ '				<label class="col-md-4 control-label">'+args.trans.buyback.config.items.sell_settings+'</label>'
						+ '				<div class="col-md-8">'
						+ '					<div class="checkbox"><label><input type="checkbox" name="sell"'+(data[0].sell ? ' checked' : '')+'>'+(data.count() == 1 ? args.trans.buyback.config.items.sell_help_1 : args.trans.buyback.config.items.sell_help_2)+'</label></div>'
						+ '				</div>'
						+ '			</div>'
						+ '			<div class="form-group">'
						+ '				<label class="col-md-4 control-label">'+args.trans.buyback.config.items.sell_modifier+'</label>'
						+ '				<div class="col-md-8">'
						+ '					<input class="form-control" type="number" name="sellModifier" min="0" step="0.01" value="'+(data[0].sellModifier)+'">'
						+ '				</div>'
						+ '			</div>'
						+ (data.count() == 1 ?
						  '			<div class="form-group">'
						+ '				<label class="col-md-4 control-label">'+args.trans.buyback.config.items.sell_price+'</label>'
						+ '				<div class="col-md-8">'
						+ '					<input class="form-control" type="number" name="sellPrice" min="0" value="'+(data[0].sellPrice)+'">'
						+ '				</div>'
						+ '			</div>' : '')
						+ '			<div class="form-group">'
						+ '				<label class="col-md-4 control-label">'+args.trans.buyback.config.items.item_settings+'</label>'
						+ '				<div class="col-md-8">'
						+ '					<div class="checkbox"><label><input type="checkbox" name="lockPrices"'+(data[0].lockPrices ? ' checked' : '')+'>'+args.trans.buyback.config.items.lock_prices_help+'</label></div>'
						+ '				</div>'
						+ '			</div>'
						+ '		</form>'
						+ '	</div>'
						+ '</div>'
					;

					bootbox.dialog({
						message: form,
						title: title,
						buttons: {
							cancel: {
								label: args.trans.buyback.config.items.cancel,
								className: "btn-default",
								callback: function() {
									//dt.ajax.reload();
								},
							},
							update: {
								label: args.trans.buyback.config.items.update,
								className: "btn-success",
								callback: function() {
									var form   = $("#manage-form-update-items");
									var action = form.attr("action");

									$.post(action, form.serialize(), function(response) {
										if (response.result == true) {
											$.notify(response.message, {className: 'success'});
											dt.ajax.reload();
										} else {
											$.notify(response.message, {className: 'error'});
										}
									});
								},
							},
						},
					});
				},
			},
			{
				text: args.trans.buyback.config.items.remove,
				action: function (e, dt, node, config) {
					var selectedRows = table.rows({selected: true});

					var message = selectedRows.count() == 1
						? args.trans.buyback.config.items.confirm_remove_1
						: args.trans.buyback.config.items.confirm_remove_2;

					var title = selectedRows.count() == 1
						? args.trans.buyback.config.items.remove_item_1
						: args.trans.buyback.config.items.remove_item_2;

					bootbox.dialog({
						message: message,
						title: title,
						buttons: {
							cancel: {
								label: args.trans.buyback.config.items.cancel,
								className: "btn-default",
								callback: function() {
									//dt.ajax.reload();
								}
							},
							remove: {
								label: args.trans.buyback.config.items.remove,
								className: "btn-danger",
								callback: function() {
									//dt.ajax.reload();
								}
							},
						},
					});

				},
				enabled: false,
			},
			{
				text: args.trans.buyback.config.items.update_prices,
				action: function (e, dt, node, config) {
					dt.ajax.reload();
				},
				enabled: true,
			},
		],
	});

	$(".dt-buttons").addClass   ('btn-group'      );
	$(".dt-button" ).addClass   ('btn btn-default');
	$(".dt-button" ).removeClass('dt-button'      );

	var tableEventFunction = function (e, dt, type, indexes) {
		var selectedRows = table.rows({selected: true}).count();

		table.button(3).enable(selectedRows >= 1); // Edit
		table.button(4).enable(selectedRows >= 1); // Remove
	};

	table.on("select"  , tableEventFunction);
	table.on("deselect", tableEventFunction);
	table.on("draw"    , tableEventFunction);
};
