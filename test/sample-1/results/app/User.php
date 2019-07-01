<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model {

	protected $table = "users";

	protected $fillable = ["name","password","email","alias","phone","description"];

	protected $hidden = ["password"];
	
}
