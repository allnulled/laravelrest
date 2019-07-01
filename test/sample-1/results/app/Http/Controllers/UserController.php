<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(${item.controller.request} $request) {
		$validator = Validator::make($request->all(), [
			"page" => "nullable|integer",
			"sort" => "nullable|string",
			"limit" => "nullable|integer",
			"fields" => "nullable|string",
		]);
		if ($validator->fails()) {
			return $this->httpError(400, [], ["message" => $validator->errors()->first()]);
		}
		$fields = $request->input("fields") ? explode(",", $request->input("fields")) : ["*"];
		$limit = $request->input("limit") ? $request->input("limit") : 20;
		$page = $request->input("page") ? $request->input("page") : 0;
		$sort = $request->input("sort") ? $request->input("sort") : "id asc";
		$group = $request->input("group") ? $request->input("group") : "id";
		$where = $request->input("where") ? $request->input("where") : null;
		$query = User::select($fields)
			->limit($limit)->offset($page * $limit)
			->orderByRaw(str_replace(":", " ", $sort))
			->groupBy(...explode(",", $group));
		if (isset($where)) {
			$whereOps = explode(",", $where);
			foreach ($whereOps as $whereOp) {
				$query = $query->where(...explode(":", $whereOp, 2));
			}
		}
		$data = $query->get();
		return $this->httpSuccess($data);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id) {
		$item = User::find($id);
		return $this->httpSuccess($item);

	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		$validator = Validator::make($request->all(), [
			"name" => "required|string|max:64|unique:user",
			"password" => "required|string|min:8|max:64",
			"email" => "required|string|max:256|email|unique:user",
			"alias" => "required|string|max:64",
			"phone" => "required|string|max:32",
			"description" => "required|string|max:510",
		]);
		if ($validator->fails()) {
			return $this->httpError(400, [], ["message" => $validator->errors()->first()]);
		}
		$item = new User($request->all());
		$item->save();
		return $this->httpSuccess($item);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id) {
		$validator = Validator::make($request->all(), [
			"name" => "nullable|string|max:64",
			"password" => "nullable|string|min:8|max:64",
			"email" => "nullable|string|max:256|email",
			"alias" => "nullable|string|max:64",
			"phone" => "nullable|string|max:32",
			"description" => "nullable|string|max:510",
		]);
		if ($validator->fails()) {
			return $this->httpError(400, [], ["message" => $validator->errors()->first()]);
		}
		$item = User::find($id);
		if (!isset($item)) {
			return $this->httpError(404, [], ["message" => "Item with id $id was not found."]);
		}
		$item->update($request->all());
		return $this->httpSuccess($item);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		$item = User::find($id);
		if (!isset($item)) {
			return $this->httpError(404, [], ["message" => "Item with id $id was not found."]);
		}
		$item->delete();
		return $this->httpSuccess($item);
	}
}
