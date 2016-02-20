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
			<a class="navbar-brand" href="#">{!! @env('APP_NAME', trans('buyback.buyback')) !!}</a>
		</div>

		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav navbar-right">
				@if(Auth::check())
					<li><a href="{!! route('contract.index') !!}">{!! ucfirst(trans_choice('buyback.contract', 2)) !!}</a></li>
					<li><a href="{!! route('manage.index'  ) !!}">{!! ucfirst(trans('buyback.manage'            )) !!}</a></li>
					<li><a href="{!! route('logout'        ) !!}">{!! ucfirst(trans('buyback.logout'            )) !!}</a></li>
				@else
					<li><a href="{!! route('login') !!}">{!! ucfirst(trans('buyback.login')) !!}</a></li>
				@endif
			</ul>
		</div><!-- /.navbar-collapse -->
	</div><!-- /.container-fluid -->
</nav>
