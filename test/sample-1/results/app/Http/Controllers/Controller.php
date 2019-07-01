<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController {
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	protected $httpResponseContext = [
		"api" => "Custom REST API",
		"version" => "1.0",
	];

	protected $httpCodes = [
		"400" => "Bad request",
		"404" => "Not found",
	];

	protected function httpSuccess($data = [], $metadata = []) {
		return response()->json(array_merge($this->httpResponseContext, [
			"success" => [
				"code" => 200,
				"status" => "OK",
			],
		], $metadata, ["data" => $data]), 200);
	}

	protected function httpError($code = 400, $metadata = [], $customError = []) {
		return response()->json(array_merge($this->httpResponseContext, [
			"error" => array_merge([
				"code" => $code,
				"status" => $this->httpCodes[$code],
			], $customError),
		], $metadata), $code);
	}

}
