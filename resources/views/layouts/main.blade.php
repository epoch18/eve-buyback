<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>{!! @env('APP_NAME', ucfirst(trans('buyback.buyback'))) !!}</title>
		<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
		@section('styles')
			<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"       rel="stylesheet" type="text/css" integrity="sha256-7s5uDGW3AHqw6xtJmNNtr+OBRJUlgkNJEo78P4b0yRw= sha512-nNo+yCHEyn0smMxSswnf/OnX6/KwJuZTlNZBjauKhTK0c+zT+q5JOCx0UFhXQ6rJR9jg6Es8gPuD2uZcYDLqSw==" crossorigin="anonymous" />
			<link href="//maxcdn.bootstrapcdn.com/bootswatch/3.3.6/cyborg/bootstrap.min.css"   rel="stylesheet" type="text/css" integrity="sha256-P5gS9tfR0S0WBWIsn0OUp1YR2pcYMGwgfwjxX3AqncA= sha512-Jwcua5M3o+swptQ5w8vJxSuiFjfuTG0mwkJAQ/XMoT8dLVr7ZyhiLxEZPwuDSTtQEl22wYbdfQAlGxd7otaCJw==" crossorigin="anonymous" />
			<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" integrity="sha256-3dkvEK0WLHRJ7/Csr0BZjAWxERc5WH7bdeUya2aXxdU= sha512-+L4yy6FRcDGbXJ9mPG8MT/3UCDzwR9gPeyFNMCtInsol++5m3bk2bXWKdZjvybmohrAsn3Ua5x8gfLnbE1YkOg==" crossorigin="anonymous" />
			<link href="//cdn.datatables.net/1.10.11/css/dataTables.bootstrap.min.css"         rel="stylesheet" type="text/css" />
			<link href="//cdn.datatables.net/buttons/1.1.2/css/buttons.dataTables.min.css"     rel="stylesheet" type="text/css" />
			<link href="//cdn.datatables.net/buttons/1.1.2/css/buttons.bootstrap.min.css"      rel="stylesheet" type="text/css" />
			<link href="//cdn.datatables.net/select/1.1.2/css/select.dataTables.min.css"       rel="stylesheet" type="text/css" />
			<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css"    rel="stylesheet" />
			<link href="/css/all.css" rel="stylesheet" type="text/css" />
			<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
			<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
			<!--[if lt IE 9]>
				<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
				<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
			<![endif]-->
		@show
	</head>
	<body>
		@include('layouts.includes.navbar')
		<section class="section-header">
			@yield('header')
		</section>
		<section class="section-content">
			@yield('content')
		</section>
		<section class="section-footer">
			@section('footer')
				<div class="col-md-12" style="text-align: center;">
					<div class="" style="display: inline-block;">
						<p><small><a href="#" onclick="CCPEVE.showInfo(1377, 94245967)">Memelo Melo</a> (<a href="https://www.github.com/msims04/eve-buyback" target="_blank">github</a>)</small></p>
					</div>
				</div>
			@show
		</section>
		@section('scripts')
			<script src="//code.jquery.com/jquery-2.1.4.min.js"                            type="text/javascript"></script>
			<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"    type="text/javascript" integrity="sha256-KXn5puMvxCw+dAYznun+drMdG1IFl3agK0p/pqT9KAo= sha512-2e8qq0ETcfWRI4HJBzQiA3UoyFk6tbNyG+qSaIBZLyW9Xf3sWZHN/lxe9fTh1U45DpPf07yj94KsUHHWe4Yk1A==" crossorigin="anonymous"></script>
			<script src="//cdn.datatables.net/s/bs/dt-1.10.10/datatables.min.js"           type="text/javascript"></script>
			<script src="//cdn.datatables.net/buttons/1.1.2/js/dataTables.buttons.min.js"  type="text/javascript"></script>
			<script src="//cdn.datatables.net/select/1.1.2/js/dataTables.select.min.js"    type="text/javascript"></script>
			<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js" type="text/javascript"></script>
			<script src="//cdnjs.cloudflare.com/ajax/libs/notify/0.4.0/notify.min.js"      type="text/javascript"></script>
			<script src="//cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.4.0/bootbox.min.js" type="text/javascript"></script>
			<script src="/js/all.js" type="text/javascript"></script>
			<script type="text/javascript">
				document.onkeydown = function(evt) {
					evt = evt || window.event;
					if (evt.keyCode == 86 && (evt.ctrlKey || evt.metaKey)) {
						var input = document.getElementById("pasteDataTextBox");
						input.focus();

						setTimeout(function() {
							form = document.getElementById("pasteForm");
							form.submit();
						}, 500);
					}
				};
			</script>
		@show
	</body>
</html>
