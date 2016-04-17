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
