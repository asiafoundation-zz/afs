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
		'survey_id'
		);
	protected $guarded = array('id');

	/* Rules */
	public static $rules = array(
		'survey_id' => 'required|numeric'
		);

	/* Database Structure */
	public static function structure()
	{
		$fields = array(
			'survey_id' => array(
				'type' => 'number',
				'onIndex' => true
			),
		);

		return compact('fields');
	}

	public function question_participants()
	{
		return $this->hasMany('QuestionPartipants');
	}

	public function filter_participants()
	{
		return $this->hasMany('FilterPartipants');
	}

}