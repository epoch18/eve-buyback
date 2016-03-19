function initIndexSelling() {
	function updateSellTotal() {
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
	});
}
