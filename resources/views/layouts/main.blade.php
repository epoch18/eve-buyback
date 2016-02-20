<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>{!! @env('APP_NAME', trans('buyback.buyback')) !!}</title>
		<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
		@section('styles')
		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"       rel="stylesheet" type="text/css" integrity="sha256-7s5uDGW3AHqw6xtJmNNtr+OBRJUlgkNJEo78P4b0yRw= sha512-nNo+yCHEyn0smMxSswnf/OnX6/KwJuZTlNZBjauKhTK0c+zT+q5JOCx0UFhXQ6rJR9jg6Es8gPuD2uZcYDLqSw==" crossorigin="anonymous" />
		<link href="//maxcdn.bootstrapcdn.com/bootswatch/3.3.6/cyborg/bootstrap.min.css"   rel="stylesheet" type="text/css" integrity="sha256-P5gS9tfR0S0WBWIsn0OUp1YR2pcYMGwgfwjxX3AqncA= sha512-Jwcua5M3o+swptQ5w8vJxSuiFjfuTG0mwkJAQ/XMoT8dLVr7ZyhiLxEZPwuDSTtQEl22wYbdfQAlGxd7otaCJw==" crossorigin="anonymous" />
		<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" integrity="sha256-3dkvEK0WLHRJ7/Csr0BZjAWxERc5WH7bdeUya2aXxdU= sha512-+L4yy6FRcDGbXJ9mPG8MT/3UCDzwR9gPeyFNMCtInsol++5m3bk2bXWKdZjvybmohrAsn3Ua5x8gfLnbE1YkOg==" crossorigin="anonymous" />
		<link href="//cdn.datatables.net/s/bs/dt-1.10.10/datatables.min.css"               rel="stylesheet" type="text/css" />
		<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css"    rel="stylesheet" />
		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
		<![endif]-->
		<style type="text/css">
			body {
				background-image: url({!! asset('img/bg01.jpg') !!});
				background-repeat: no-repeat;
				background-position: left top;
				background-attachment: fixed;
			}
		</style>
		@show
	</head>
	<body>
		@include('layouts.includes.navbar')
		<section class="section-header">
			@section('header')
			@show
		</section>
		<section class="section-content">
			@yield('content')
		</section>
		<section class="section-footer">
			@yield('footer')
		</section>
		@section('scripts')
		<script src="//code.jquery.com/jquery-2.1.4.min.js"                            type="text/javascript"></script>
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"    type="text/javascript" integrity="sha256-KXn5puMvxCw+dAYznun+drMdG1IFl3agK0p/pqT9KAo= sha512-2e8qq0ETcfWRI4HJBzQiA3UoyFk6tbNyG+qSaIBZLyW9Xf3sWZHN/lxe9fTh1U45DpPf07yj94KsUHHWe4Yk1A==" crossorigin="anonymous"></script>
		<script src="//cdn.datatables.net/s/bs/dt-1.10.10/datatables.min.js"           type="text/javascript"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js" type="text/javascript"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				if (!String.prototype.format) {
					String.prototype.format = function() {
					var args = arguments;
					return this.replace(/{(\d+)}/g, function(match, number) {
						return typeof args[number] != 'undefined'
							? args[number] : match; });
					};
				} });
		</script>
		@show
	</body>
</html>
