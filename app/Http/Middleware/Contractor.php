<?php

namespace App\Http\Middleware;

use Closure;

class Contractor
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if (!auth()->user()->isContractor() && !auth()->user()->isAdministrator()) {
			return response('Unauthorized.', 401);
		}

		return $next($request);
	}
}
