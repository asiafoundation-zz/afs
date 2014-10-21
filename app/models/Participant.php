<?php
class Participant extends Eloquent {

	/* Soft Delete */
	protected $softDelete = true;

	/* Eloquent */
	public $table = "participants";
	public $timestamps = true;


	/* Disabled Basic Actions */
	public static $disabledActions = array();

	/* Route */
	public $route = 'participant';

	/* Mass Assignment */
	protected $fillable = array(
		'sample_type'
		);
	protected $guarded = array('id');

	/* Rules */
	public static $rules = array(
		'sample_type' => 'required|numeric'
		);

	/* Database Structure */
	public static function structure()
	{
		$fields = array(
			'sample_type' => array(
				'type' => 'number',
				'onIndex' => true
			),
		);

		return compact('fields');
	}


}