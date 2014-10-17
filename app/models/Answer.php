<?php
class Answer extends Eloquent {

	/* Soft Delete */
	use SoftDeletingTrait;
	protected $dates = ['deleted_at'];

	/* Eloquent */
	public $table = "answers";
	public $timestamps = true;


	/* Disabled Basic Actions */
	public static $disabledActions = array();

	/* Route */
	public $route = 'answer';

	/* Mass Assignment */
	protected $fillable = array(
		'answer',
		'question_id',
		'color_id'
		);
	protected $guarded = array('id');

	/* Rules */
	public static $rules = array(
		'answer' => 'required',
		'question_id' => 'required|numeric',
		'color_id' => 'required|numeric'
		);

	/* Database Structure */
	public static function structure()
	{
		$fields = array(
			'answer' => array(
			'type' => 'text',
			'onIndex' => true
			),
			'question_id' => array(
					'type' => 'number',
					'onIndex' => true
			),
			'color_id' => array(
						'type' => 'number',
						'onIndex' => true
			)
		);

		return compact('fields');
	}

}