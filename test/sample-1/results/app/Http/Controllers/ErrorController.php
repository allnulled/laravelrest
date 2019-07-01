<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;

class ErrorController extends Controller {
	public function endpointNotFound() {
		return $this->httpError(404, [], ["message" => "Endpoint not found"]);
	}

	public function notFound() {
		return $this->httpError(404, [], ["message" => "Resource not found"]);
	}

	public function badRequest() {
		return $this->httpError(400, []);
	}
}
