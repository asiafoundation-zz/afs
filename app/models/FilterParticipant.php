<?php
class FilterParticipant extends Eloquent {

	/* Soft Delete */
	protected $softDelete = true;

	/* Eloquent */
	public $table = "filter_participants";
	public $timestamps = true;


	/* Disabled Basic Actions */
	public static $disabledActions = array();

	/* Route */
	public $route = 'filter_participant';

	/* Mass Assignment */
	protected $fillable = array(
		'region_id'
		);
	protected $guarded = array('id');

	/* Rules */
	public static $rules = array(
		'region_id' => 'required|numeric'
		);

	/* Database Structure */
	public static function structure()
	{
		$fields = array(
			'answer' => array(
				'type' => 'text',
				'onIndex' => true
			),
			'region_id' => array(
				'type' => 'number',
				'onIndex' => true
			),
		);

		return compact('fields');
	}


}