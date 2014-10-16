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
		'region_id',
		'cycle_id'
		);
	protected $guarded = array('id');

	/* Rules */
	public static $rules = array(
		'region_id' => 'required|numeric',
		'cycle_id' => 'required|numeric'
		);

	/* Database Structure */
	public static function structure()
	{
		$fields = array(
			'region_id' => array(
				'type' => 'number',
				'onIndex' => true
			),
			'cycle_id' => array(
				'type' => 'number',
				'onIndex' => true
			),
		);

		return compact('fields');
	}


}