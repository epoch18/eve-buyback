<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	/**
	 * Returns the error response for requests not using ajax.
	 * @return \Illuminate\Http\JsonResponse
	 */
	protected function ajaxErrorResponse()
	{
		return $this->ajaxFailureResponse(
			trans('buyback.messages.ajax_requests_only'));
	}

	/**
	 * Returns the response for failed requests.
	 * @return \Illuminate\Http\JsonResponse
	 */
	protected function ajaxFailureResponse($message, $status = 500)
	{
		return response()->json([
			'result'    => false,
			'message'   => $message,
		], $status);
	}

	/**
	 * Returns the response for successful requests.
	 * @return \Illuminate\Http\JsonResponse
	 */
	protected function ajaxSuccessResponse($message, $status = 200)
	{
		return response()->json([
			'result'  => true,
			'message' => $message,
		], $status);
	}
}
