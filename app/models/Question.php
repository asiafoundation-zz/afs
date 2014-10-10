<?php
class Question extends Eloquent {

	/* Soft Delete */
	protected $softDelete = true;

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
		'question_category_id',
		'cycle_id',
		'is_default'
		);
	protected $guarded = array('id');

	/* Rules */
	public static $rules = array(
		'code' => 'required',
		'question' => 'required',
		'question_category_id' => 'required',
		'cycle_id' => 'required',
		'is_default' => 'required'
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
			'question_category_id' => array(
					'type' => 'number',
					'onIndex' => true
			),
			'cycle_id' => array(
				'type' => 'number',
				'onIndex' => true
			),
			'is_default' => array(
				'type' => 'number',
				'onIndex' => true
			)
		);

		return compact('fields');
	}

	public static function LoadQuestion($request = array())
	{
		$questions =  DB::table('questions')
			->select(
				DB::raw(
					'questions.id as id_question,
					questions.question as question,
					question_categories.id as id_question_categories,
					question_categories.name as question_categories,
					cycles.id  as id_cycle,
					cycles.name as cycle,
					answers.id  as id_answer,
					answers.answer as answer,
					colors.color,
					(SELECT SUM(questioners.amount) FROM questioners where questioners.answer_id = id_answer GROUP BY answer_id) AS amount'
					)
				)
			->join('question_categories','question_categories.id','=','questions.question_category_id')
			->join('cycles','cycles.id','=','questions.cycle_id')
			->join('answers','answers.question_id','=','questions.id')
			->join('colors','colors.id','=','answers.color_id')
			->join('questioners','questioners.answer_id','=','questioners.id');

			if (count($request)) {
				if (isset($request['region'])) {
					$questions =  $questions->where('questioners.region_id', '=', $request['region']);
				}
				if (isset($request['category'])) {
					$questions =  $questions->where('question_categories.id', '=', $request['category']);
				}
				if (isset($request['question'])) {
					$questions =  $questions->where('questions.id', '=', $request['question']);
				}
				if (isset($request['cycle'])) {
					$questions =  $questions->where('cycles.id', '=', $request['cycle']);
				}
			}
			else
			{
				$questions =  $questions->where('questions.is_default', '=', 1);
			}

			$questions =  $questions
			->groupBy('answer')
			->get();

		return $questions;
	}
}