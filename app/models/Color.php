<?php
class Color extends Eloquent {

	/* Soft Delete */
	use SoftDeletingTrait;
	protected $dates = ['deleted_at'];

	/* Eloquent */
	public $table = "colors";
	public $timestamps = true;

	/* Disabled Basic Actions */
	public static $disabledActions = array();

	/* Route */
	public $route = 'color';

	/* Mass Assignment */
	protected $fillable = array(
		'color'
		);
	protected $guarded = array('id');

	/* Rules */
	public static $rules = array(
		'color' => 'required'
		);

	/* Database Structure */
	public static function structure()
	{
		$fields = array(
			'color' => array(
			'type' => 'text',
			'onIndex' => true
		)
			);

		return compact('fields');
	}


}