<?php
class Question extends Eloquent {

	/* Soft Delete */
	use SoftDeletingTrait;
	protected $dates = ['deleted_at'];

	/* Eloquent */
	public $table = "questions";
	public $timestamps = true;

	

	

	/* Disabled Basic Actions */
	public static $disabledActions = array();

	/* Route */
	public $route = 'question';

	/* Mass Assignment */
	protected $fillable = array(
		'code',
'question',
'survey_id'
		);
	protected $guarded = array('id');

	/* Rules */
	public static $rules = array(
		'code' => 'required|numeric',
'question' => 'required',
'survey_id' => 'required|numeric'
		);

	/* Database Structure */
	public static function structure()
	{
		$fields = array(
			'code' => array(
			'type' => 'number',
			'onIndex' => true
		),
'question' => array(
			'type' => 'text',
			'onIndex' => true
		),
'survey_id' => array(
			'type' => 'number',
			'onIndex' => true
		)
			);

		return compact('fields');
	}


}