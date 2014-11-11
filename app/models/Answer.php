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
		'color_id',
		'cycle_id',
		'cycle_default'
		);
	protected $guarded = array('id');

	/* Rules */
	public static $rules = array(
		'answer' => 'required',
		'question_id' => 'required|numeric',
		'color_id' => 'required|numeric',
		'cycle_id' => 'required|numeric',
		'cycle_default' => 'required|numeric'
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
			),
			'cycle_id' => array(
						'type' => 'number',
						'onIndex' => true
			),
			'cycle_default' => array(
						'type' => 'number',
						'onIndex' => true
			)
		);

		return compact('fields');
	}

	public static function checkData($data,$question_id, $cycle_id, $color_id)
	{
		$answer = Answer::where('answer', '=', $data)->where('cycle_id', '=', $cycle_id)->first();
		if(!isset($answer))
		{
			$answer = Answer::create(array('answer' => $data, 'question_id' => $question_id, 'color_id' => rand(1,4),'cycle_id' => $cycle_id));
		}

		return $answer;
	}
}