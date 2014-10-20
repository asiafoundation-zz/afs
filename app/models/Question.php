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

	public static function DefaultQuestion($request = array())
	{
		$questions =  DB::table('questions')
			->select(
				DB::raw(
					'questions.id as id_question,
					questions.question as question,
					question_categories.id as id_question_categories,
					question_categories.name as question_categories,
					cycles.id  as id_cycle,
					cycles.id  as cycle_type,
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
				if (!empty($request['category'])) {
					$questions =  $questions->where('question_categories.id', '=', $request['category']);
				}
				if (!empty($request['cycle'])) {
					$questions =  $questions->where('cycles.id', '=', $request['cycle']);
				}
				if (!empty($request['question'])) {
					$questions =  $questions->where('questions.question', '=', $request['question']);
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

	public static function LoadQuestion($request = array())
	{
		$questions =  DB::table('questions')
			->select(
				DB::raw(
					'questions.id as id_question,
					questions.question as question,
					question_categories.id as id_question_categories,
					question_categories.name as question_categories,
					regions.id as id_region,
					cycles.id  as id_cycle,
					cycles.id  as cycle_type,
					cycles.name as cycle,
					answers.id  as id_answer,
					answers.answer as answer,
					colors.color,
					(SELECT SUM(questioners.amount) FROM questioners where questioners.answer_id = id_answer and questioners.region_id = id_region GROUP BY answer_id) AS amount'
					)
				)
			->join('question_categories','question_categories.id','=','questions.question_category_id')
			->join('cycles','cycles.id','=','questions.cycle_id')
			->join('answers','answers.question_id','=','questions.id')
			->join('colors','colors.id','=','answers.color_id')
			->join('questioners','questioners.question_id','=','questions.id')
			->leftJoin('regions','regions.id','=','questioners.region_id');

			if (count($request)) {
				if ($request['region'] != "null") {
					$questions =  $questions->where('regions.name', '=', (string)$request['region']);
				}
				if (!empty($request['category'])) {
					$questions =  $questions->where('question_categories.id', '=', $request['category']);
				}
				if (!empty($request['question'])) {
					$questions =  $questions->where('questions.question', '=', $request['question']);
				}
				if (!empty($request['cycle'])) {
					$questions =  $questions->where('cycles.id', '=', $request['cycle']);
				}
			}

			$questions =  $questions
			->groupBy('answer')
			->get();

		return $questions;
	}

	public static function FilterQuestion($request = array())
	{
		$questions =  DB::table('questions')
			->select(
				DB::raw(
					'questions.id as id_question,
					questions.question as question,
					question_categories.id as id_question_categories,
					question_categories.name as question_categories,
					answers.id  as id_answer,
					answers.answer as answer,
					participants.id as participant_id,
					colors.color,
					0 AS amount'
					)
				)
			->join('question_categories','questions.question_category_id','=','question_categories.id')
			->join('answers','answers.question_id','=','questions.id')
			->join('colors','answers.color_id','=','colors.id')
			->join('question_participants','question_participants.participant_id','=','answers.id')
			->join('participants','participants.id','=','question_participants.participant_id')
			->join('filter_participants','filter_participants.participant_id','=','participants.id')
			;

			if (count($request)) {
				if (!empty($request['category'])) {
					$questions =  $questions->where('question_categories.id', '=', $request['category']);
				}
				if (!empty($request['question'])) {
					$questions =  $questions->where('questions.question', '=', $request['question']);
				}
			}

			$questions =  $questions
			->groupBy('answer')
			->get();

			foreach ($questions as $key_questions => $question) {
				$question->amount = FilterParticipant::FilterOptions($question->id_answer,$request);
			}

		return $questions;
	}
}