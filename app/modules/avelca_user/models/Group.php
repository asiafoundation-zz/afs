<?php namespace App\Modules\Avelca_User\Models;

class Group extends \Eloquent {

	/* Soft Delete */
	use \SoftDeletingTrait;
	protected $dates = ['deleted_at'];

	/* Eloquent */
	protected $table = "groups";
	public $timestamps = true;
	
}