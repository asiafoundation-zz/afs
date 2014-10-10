<?php
class Questioner extends Eloquent {

	/* Soft Delete */
	protected $softDelete = true;

	/* Eloquent */
	public $table = "questioners";
	public $timestamps = true;

	/* Disabled Basic Actions */
	public static $disabledActions = array();

	/* Route */
	public $route = 'questioner';

	/* Mass Assignment */
	protected $fillable = array(
		'amount',
		'answer_id',
		'region_id'
		);
	protected $guarded = array('id');

	/* Rules */
	public static $rules = array(
		'amount' => 'required|numeric',
		'answer_id' => 'required|numeric',
		'region_id' => 'required|numeric'
		);

	/* Database Structure */
	public static function structure()
	{
		$fields = array(
			'amount' => array(
				'type' => 'number',
				'onIndex' => true
				),
			'answer_id' => array(
					'type' => 'number',
					'onIndex' => true
				),
			'region_id' => array(
					'type' => 'number',
					'onIndex' => true
				)
		);

		return compact('fields');
	}


}