<?php
class QuestionParticipant extends Eloquent {

	/* Soft Delete */
	protected $softDelete = true;

	/* Eloquent */
	public $table = "question_participants";
	public $timestamps = true;


	/* Disabled Basic Actions */
	public static $disabledActions = array();

	/* Route */
	public $route = 'question_participant';

	/* Mass Assignment */
	protected $fillable = array(
		'category_item_id',
		'participant_id'
		);
	protected $guarded = array('id');

	/* Rules */
	public static $rules = array(
		'category_item_id' => 'required|numeric',
		'participant_id' => 'required|numeric'
		);

	/* Database Structure */
	public static function structure()
	{
		$fields = array(
			'category_item_id' => array(
				'type' => 'number',
				'onIndex' => true
			),
			'participant_id' => array(
				'type' => 'number',
				'onIndex' => true
			),
		);

		return compact('fields');
	}


}