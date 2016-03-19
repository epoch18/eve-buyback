<nav class="navbar navbar-default">
	<div class="container-fluid">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="/">{!! @env('APP_NAME', trans('buyback.headers.buyback')) !!}</a>
		</div>

		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav navbar-right">
				@if(Auth::check())
					@can('contract')
						<li><a href="{!! route('contract.index') !!}">{!! trans_choice('buyback.headers.contracts', 2) !!}</a></li>
					@endcan
					@can('administrate')
						<li><a href="{!! route('manage.index') !!}">{!! trans('buyback.headers.manage') !!}</a></li>
					@endcan
					<li><a href="{!! route('logout') !!}">{!! trans('buyback.headers.logout') !!}</a></li>
				@else
					<li><a href="{!! route('login') !!}">{!! trans('buyback.headers.login') !!}</a></li>
				@endif
			</ul>
		</div><!-- /.navbar-collapse -->
	</div><!-- /.container-fluid -->
</nav>
