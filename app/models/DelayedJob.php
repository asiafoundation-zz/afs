<?php
class DelayedJob extends Eloquent {

	/* Eloquent */
	public $table = "delayed_jobs";
	public $timestamps = true;


	/* Disabled Basic Actions */
	public static $disabledActions = array();

	/* Route */
	public $route = 'delayed_job';

	/* Mass Assignment */
	protected $fillable = array(
		'type',
		'data',
		'survey_id',
		'queue',
		'information'
		);
	protected $guarded = array('id');

	/* Rules */
	public static $rules = array(
		'data' => 'required',
		'type' => 'required',
		'survey_id' => 'required',
		'queue' => 'required',
		'information' => 'required'
		);

	/* Database Structure */
	public static function structure()
	{
		$fields = array(
			'type' => array(
			'type' => 'text',
			'onIndex' => true
			),
			'data' => array(
			'type' => 'text',
			'onIndex' => true
			),
			'survey_id' => array(
			'type' => 'number',
			'onIndex' => true
			),
			'queue' => array(
			'type' => 'number',
			'onIndex' => true
			),
			'information' => array(
			'type' => 'text',
			'onIndex' => true
			)
		);

		return compact('fields');
	}
}