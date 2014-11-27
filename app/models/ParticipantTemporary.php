<?php

class ParticipantTemporary extends MongoLid {
	protected $database = 'asia_foundation_survey';
	protected $collection = 'participants_collections';
	/* Mass Assignment */
	public $fillable = array(
		'survey_id',
		'delayed_job_id',
		'data'
		);
	public $guarded = array('id');

	/* Rules */
	public static $rules = array(
		'survey_id' => 'required',
		'delayed_job_id' => 'required',
		'data' => 'required'
		);

	/* Database Structure */
	public static function structure()
	{
		$fields = array(
			'survey_id' => array(
				'type' => 'number',
				'onIndex' => true
			),
			'delayed_job_id' => array(
				'type' => 'number',
				'onIndex' => true
			),
			'data' => array(
				'type' => 'text',
				'onIndex' => true
			),
		);
	}
}