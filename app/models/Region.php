<?php
class Region extends Eloquent {

	/* Soft Delete */
	use SoftDeletingTrait;
	protected $dates = ['deleted_at'];

	/* Eloquent */
	public $table = "regions";
	public $timestamps = true;

	

	

	/* Disabled Basic Actions */
	public static $disabledActions = array();

	/* Route */
	public $route = 'region';

	/* Mass Assignment */
	protected $fillable = array(
		'regionscol'
		);
	protected $guarded = array('id');

	/* Rules */
	public static $rules = array(
		'regionscol' => 'required'
		);

	/* Database Structure */
	public static function structure()
	{
		$fields = array(
			'regionscol' => array(
			'type' => 'text',
			'onIndex' => true
		)
			);

		return compact('fields');
	}


}