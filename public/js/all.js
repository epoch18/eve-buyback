function collect() {
	var ret = {};
	var len = arguments.length;
	for (var i=0; i<len; i++) {
		for (p in arguments[i]) {
			if (arguments[i].hasOwnProperty(p)) {
				ret[p] = arguments[i][p];
			}
		}
	}
	return ret;
}

$(document).ready(function() {
	if (!String.prototype.format) {
		String.prototype.format = function() {
			var args = arguments;

			return this.replace(/{(\d+)}/g, function(match, number) {
				return typeof args[number] != 'undefined' ? args[number] : match;
			});
		};
	}
});

function initManageMotd(args) {
	var form = $("#manage-form-motd");

	form.submit(function () {
		var button = $("#manage-form-motd #submit");

		button.prop('disabled', true);
		button.html('<span class="fa fa-spinner fa-spin"></span> ' + args.trans.buttons.edit);

		$.ajax({
			type: "POST",
			url: args.actions.editMotd,
			data: form.serialize(),

			success: function(response) {
				$.notify(response.message, {className: 'success'});

				button.prop('disabled', false);
				button.html(args.trans.buttons.edit);
			},

			error: function(request, status, error) {
				var response = JSON.parse(request.responseText);

				$.notify(response.message, {className: 'error'});

				button.prop('disabled', false);
				button.html(args.trans.buttons.edit);
			},
		});

		return false;
	});
}

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
				data: "source",
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
				text: args.trans.buttons.add,
				action: function (e, dt, node, config) {
					var form = ''
						+ '<div class="row">'
						+ '	<div class="col-md-12">'
						+ '		<form id="manage-form-add-items" class="form-horizontal">'
						+ '			<input type="hidden" name="_token" value="'+args.token+'">'
						+ '			<div class="form-group">'
						+ '				<label class="col-md-4 control-label">'+args.trans.headers.types.split('|')[1]+'</label>'
						+ '				<div class="col-md-8">'
						+ '					<select id="types" name="types[]" class="form-control" multiple="multiple"></select>'
						+ '				</div>'
						+ '			</div>'
						+ '			<div class="form-group">'
						+ '				<label class="col-md-4 control-label">'+args.trans.headers.groups.split('|')[1]+'</label>'
						+ '				<div class="col-md-8">'
						+ '					<select id="groups" name="groups[]" class="form-control" multiple="multiple"></select>'
						+ '				</div>'
						+ '			</div>'
						+ '			<div class="form-group">'
						+ '				<label class="col-md-4 control-label">'+args.trans.headers.categories.split('|')[1]+'</label>'
						+ '				<div class="col-md-8">'
						+ '					<select id="categories" name="categories[]" class="form-control" multiple="multiple"></select>'
						+ '				</div>'
						+ '			</div>'
						// Copy of update items form below minus prices.
						+ '			<div class="form-group">'
						+ '				<label class="col-md-4 control-label">'+args.trans.headers.buy_settings+'</label>'
						+ '				<div class="col-md-8">'
						+ '					<div class="checkbox"><label><input type="checkbox" name="buyRaw"     >'+args.trans.messages.buy_raw     .split('|')[1]+'</label></div>'
						+ '					<div class="checkbox"><label><input type="checkbox" name="buyRecycled">'+args.trans.messages.buy_recycled.split('|')[1]+'</label></div>'
						+ '					<div class="checkbox"><label><input type="checkbox" name="buyRefined" >'+args.trans.messages.buy_refined .split('|')[1]+'</label></div>'
						+ '				</div>'
						+ '			</div>'
						+ '			<div class="form-group">'
						+ '				<label class="col-md-4 control-label">'+args.trans.headers.buy_modifier+'</label>'
						+ '				<div class="col-md-8">'
						+ '					<input class="form-control" type="number" name="buyModifier" min="0" step="0.01" value="">'
						+ '				</div>'
						+ '			</div>'
						+ '			<div class="form-group">'
						+ '				<label class="col-md-4 control-label">'+args.trans.headers.sell_settings+'</label>'
						+ '				<div class="col-md-8">'
						+ '					<div class="checkbox"><label><input type="checkbox" name="sell">'+args.trans.messages.sell_items.split('|')[1]+'</label></div>'
						+ '				</div>'
						+ '			</div>'
						+ '			<div class="form-group">'
						+ '				<label class="col-md-4 control-label">'+args.trans.headers.sell_modifier+'</label>'
						+ '				<div class="col-md-8">'
						+ '					<input class="form-control" type="number" name="sellModifier" min="0" step="0.01" value="">'
						+ '				</div>'
						+ '			</div>'
						+ '			<div class="form-group">'
						+ '				<label class="col-md-4 control-label">'+args.trans.headers.item_settings+'</label>'
						+ '				<div class="col-md-8">'
						+ '					<div class="checkbox"><label><input type="checkbox" name="lockPrices">'+args.trans.messages.lock_prices+'</label></div>'
						+ '				</div>'
						+ '			</div>'
						+ '			<div class="form-group">'
						+ '				<label class="col-md-4 control-label">'+args.trans.headers.source+'</label>'
						+ '				<div class="col-md-8">'
						+ '					<select id="source" name="source" class="form-control">'
						+ '					<option selected="selected">Jita</option>'
						+ '					<option>1DQ1-A</option>'
						+ '					</select>'
						+ '				</div>'
						+ '			</div>'
						// End of update items form.
						+ '		</form>'
						+ '	</div>'
						+ '</div>'
					;

					var box = bootbox.dialog({
						message: form,
						title: args.trans.messages.add_items.split('|')[1],
						buttons: {
							cancel: {
								label: args.trans.buttons.cancel,
								className: "btn-default",
								callback: function() {
								},
							},
							update: {
								label: args.trans.buttons.add,
								className: "btn-success",
								callback: function() {
									var form   = $("#manage-form-add-items");
									var button = table.button(2);

									button.enable(false);
									button.text('<span class="fa fa-spinner fa-spin"></span> ' + args.trans.buttons.add);

									$.ajax({
										type: "POST",
										url: args.actions.addItems,
										data: form.serialize(),

										success: function(response) {
											$.notify(response.message, {className: 'success'});

											button.enable(true);
											button.text(args.trans.buttons.add);

											dt.ajax.reload();
										},

										error: function(request, status, error) {
											var response = JSON.parse(request.responseText);

											$.notify(response.message, {className: 'error'});

											button.enable(true);
											button.text(args.trans.buttons.add);

											dt.ajax.reload();
										},
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
						}); // #manage-form-add-items #types

						$("#manage-form-add-items #groups").select2({
							ajax: {
								url: args.actions.getGroups,
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
										obj.id   = obj.groupID;
										obj.text = obj.groupName;
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

								return $('<span>'+state.groupName+'</span>');
							},
						}); // #manage-form-add-items #groups

						$("#manage-form-add-items #categories").select2({
							ajax: {
								url: args.actions.getCategories,
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
										obj.id   = obj.categoryID;
										obj.text = obj.categoryName;
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

								return $('<span>'+state.categoryName+'</span>');
							},
						}); // #manage-form-add-items #categories
					});

					dt.ajax.reload();
				},
				enabled: true,
			},
			{
				text: args.trans.buttons.edit,
				action: function (e, dt, node, config) {
					var data  = table.rows('.selected').data();
					var types = "";

					$.each(data, function (index, item) {
						types += item.typeID + ",";
					}); types  = types.substr(0, types.length - 1);

					var title = args.trans.messages.edit_items.split('|')[data.count() == 1 ? 0 : 1];

					var form = ''
						+ '<div class="row">'
						+ '	<div class="col-md-12">'
						+ '		<form id="manage-form-update-items" class="form-horizontal" action="'+args.actions.editItems+'" method="POST">'
						+ '			<input type="hidden" name="_token" value="'+args.token+'">'
						+ '			<input type="hidden" name="items" value="'+(types)+'">'
						+ '			<div class="form-group">'
						+ '				<label class="col-md-4 control-label">'+args.trans.headers.buy_settings+'</label>'
						+ '				<div class="col-md-8">'
						+ '					<div class="checkbox"><label><input type="checkbox" name="buyRaw"     '+(data[0].buyRaw      ? ' checked' : '')+'>'+args.trans.messages.buy_raw     .split('|')[data.count() == 1 ? 0 : 1]+'</label></div>'
						+ '					<div class="checkbox"><label><input type="checkbox" name="buyRecycled"'+(data[0].buyRecycled ? ' checked' : '')+'>'+args.trans.messages.buy_recycled.split('|')[data.count() == 1 ? 0 : 1]+'</label></div>'
						+ '					<div class="checkbox"><label><input type="checkbox" name="buyRefined" '+(data[0].buyRefined  ? ' checked' : '')+'>'+args.trans.messages.buy_refined .split('|')[data.count() == 1 ? 0 : 1]+'</label></div>'
						+ '				</div>'
						+ '			</div>'
						+ '			<div class="form-group">'
						+ '				<label class="col-md-4 control-label">'+args.trans.headers.buy_modifier+'</label>'
						+ '				<div class="col-md-8">'
						+ '					<input class="form-control" type="number" name="buyModifier" min="0" step="0.01" value="'+(data[0].buyModifier)+'">'
						+ '				</div>'
						+ '			</div>'
						+ (data.count() == 1 ?
						  '			<div class="form-group">'
						+ '				<label class="col-md-4 control-label">'+args.trans.headers.buy_price+'</label>'
						+ '				<div class="col-md-8">'
						+ '					<input class="form-control" type="number" name="buyPrice" min="0" value="'+(data[0].buyPrice)+'">'
						+ '				</div>'
						+ '			</div>' : '')
						+ '			<div class="form-group">'
						+ '				<label class="col-md-4 control-label">'+args.trans.headers.sell_settings+'</label>'
						+ '				<div class="col-md-8">'
						+ '					<div class="checkbox"><label><input type="checkbox" name="sell"'+(data[0].sell ? ' checked' : '')+'>'+args.trans.messages.sell_items.split('|')[1]+'</label></div>'
						+ '				</div>'
						+ '			</div>'
						+ '			<div class="form-group">'
						+ '				<label class="col-md-4 control-label">'+args.trans.headers.sell_modifier+'</label>'
						+ '				<div class="col-md-8">'
						+ '					<input class="form-control" type="number" name="sellModifier" min="0" step="0.01" value="'+(data[0].sellModifier)+'">'
						+ '				</div>'
						+ '			</div>'
						+ (data.count() == 1 ?
						  '			<div class="form-group">'
						+ '				<label class="col-md-4 control-label">'+args.trans.headers.sell_price+'</label>'
						+ '				<div class="col-md-8">'
						+ '					<input class="form-control" type="number" name="sellPrice" min="0" value="'+(data[0].sellPrice)+'">'
						+ '				</div>'
						+ '			</div>' : '')
						+ '			<div class="form-group">'
						+ '				<label class="col-md-4 control-label">'+args.trans.headers.item_settings+'</label>'
						+ '				<div class="col-md-8">'
						+ '					<div class="checkbox"><label><input type="checkbox" name="lockPrices"'+(data[0].lockPrices ? ' checked' : '')+'>'+args.trans.messages.lock_prices+'</label></div>'
						+ '				</div>'
						+ '			</div>'
						+ '			<div class="form-group">'
						+ '				<label class="col-md-4 control-label">'+args.trans.headers.source+'</label>'
						+ '				<div class="col-md-8">'
						+ '					<select id="source" name="source" class="form-control">'
						+ '					<option selected="selected">Jita</option>'
						+ '					<option>1DQ1-A</option>'
						+ '					</select>'
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
								label: args.trans.buttons.cancel,
								className: "btn-default",
								callback: function() {
								},
							},
							update: {
								label: args.trans.buttons.edit,
								className: "btn-success",
								callback: function() {
									var form   = $("#manage-form-update-items");
									var button = table.button(3);

									button.enable(false);
									button.text('<span class="fa fa-spinner fa-spin"></span> ' + args.trans.buttons.edit);

									$.ajax({
										type: "POST",
										url: args.actions.editItems,
										data: form.serialize(),

										success: function(response) {
											$.notify(response.message, {className: 'success'});

											button.enable(true);
											button.text(args.trans.buttons.edit);

											dt.ajax.reload();
										},

										error: function(request, status, error) {
											var response = JSON.parse(request.responseText);

											$.notify(response.message, {className: 'error'});

											button.enable(true);
											button.text(args.trans.buttons.edit);

											dt.ajax.reload();
										},
									});
								},
							},
						},
					});
				},
				enabled: true,
			},
			{
				text: args.trans.buttons.remove,
				action: function (e, dt, node, config) {
					var data  = table.rows('.selected').data();
					var types = "";

					$.each(data, function (index, item) {
						types += item.typeID + ",";
					}); types  = types.substr(0, types.length - 1);

					var message = args.trans.messages.remove_items_confirm.split('|')[data.count() == 1 ? 0 : 1];
					var title   = args.trans.messages.remove_items        .split('|')[data.count() == 1 ? 0 : 1];

					bootbox.dialog({
						message: message,
						title: title,
						buttons: {
							cancel: {
								label: args.trans.buttons.cancel,
								className: "btn-default",
								callback: function() {
								},
							},
							remove: {
								label: args.trans.buttons.remove,
								className: "btn-danger",
								callback: function() {
									var button = table.button(4);

									button.enable(false);
									button.text('<span class="fa fa-spinner fa-spin"></span> ' + args.trans.buttons.remove);

									$.ajax({
										type: "POST",
										url: args.actions.removeItems,
										data: {_token: args.token, types: types},

										success: function(response) {
											$.notify(response.message, {className: 'success'});

											button.enable(true);
											button.text(args.trans.buttons.remove);

											dt.ajax.reload();
										},

										error: function(request, status, error) {
											var response = JSON.parse(request.responseText);

											$.notify(response.message, {className: 'error'});

											button.enable(true);
											button.text(args.trans.buttons.remove);

											dt.ajax.reload();
										},
									});
								}
							},
						},
					});
				},
				enabled: false,
			},
			{
				text: args.trans.buttons.update_prices,
				action: function (e, dt, node, config) {
					var button = table.button(5);

					button.enable(false);
					button.text('<span class="fa fa-spinner fa-spin"></span> ' + args.trans.buttons.update_prices);

					$.ajax({
						type: "POST",
						url: args.actions.updateItems,
						data: {_token: args.token},

						success: function(response) {
							$.notify(response.message, {className: 'success'});

							button.enable(true);
							button.text(args.trans.buttons.update_prices);

							dt.ajax.reload();
						},

						error: function(request, status, error) {
							var response = JSON.parse(request.responseText);

							$.notify(response.message, {className: 'error'});

							button.enable(true);
							button.text(args.trans.buttons.update_prices);

							dt.ajax.reload();
						},
					});
				},
				enabled: true,
			},
		],
	});

	$(".dt-buttons").addClass   ("btn-group"      );
	$(".dt-button" ).addClass   ("btn btn-default");
	$(".dt-button" ).removeClass("dt-button"      );

	var tableEventFunction = function (e, dt, type, indexes) {
		var selectedRows = table.rows({selected: true}).count();

		table.button(3).enable(selectedRows >= 1); // Edit
		table.button(4).enable(selectedRows >= 1); // Remove
	};

	table.on("select"  , tableEventFunction);
	table.on("deselect", tableEventFunction);
	table.on("draw"    , tableEventFunction);
};

function initMiningTable(args) {
	var table = $('#mining-table').DataTable({
		ajax: args.actions.getAsteroids,
		sAjaxDataProp: "",
		order: [[ 3, "desc" ]],
		columns: [
			{
				data: "typeName",
				render: function (data, type, row, meta) {
					if (type != "display") { return data; }

					return '<img src="https://image.eveonline.com/Type/'+row.typeID+'_32.png"> '+data;
				},
			},
			{
				data: "groupName",
			},
			{
				data: "categoryName",
			},
			{
				data: "price",
				render: function (data, type, row, meta) {
					if (type != "display") { return data; }

					return data.toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")+' ISK';
				},
			},
		],
	});
};

//# sourceMappingURL=all.js.map
