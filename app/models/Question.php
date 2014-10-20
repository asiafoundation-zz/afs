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
					answers.id  as id_answer,
					answers.answer as answer,
					participants.id as participant_id,
					colors.color,
					cycles.id  as id_cycle,
					cycles.id  as cycle_type,
					cycles.name as cycle,
					0 AS amount,
					0 AS indexlabel'
					)
				)
			->join('question_categories','questions.question_category_id','=','question_categories.id')
			->join('answers','answers.question_id','=','questions.id')
			->join('colors','answers.color_id','=','colors.id')
			->join('question_participants','question_participants.participant_id','=','answers.id')
			->join('participants','participants.id','=','question_participants.participant_id')
			->join('cycles','cycles.id','=','participants.cycle_id')
			->join('filter_participants','filter_participants.participant_id','=','participants.id')
			;

			if (!empty($request['cycle'])) {
				if (!empty($request['region'])) {
					$questions =  $questions->where('regions.name', '=', (string)$request['region']);
				}
				if (!empty($request['category'])) {
					$questions =  $questions->where('question_categories.id', '=', $request['category']);
				}
				if (!empty($request['question'])) {
					$questions =  $questions->where('questions.id', '=', $request['question']);
				}

			}
			else{
				$questions = $questions->where('cycles.cycle_type', '=',0)
					->where('questions.is_default', '=', 1);
			}

			$questions = $questions->groupBy('answer')
			->get();

			// Count question amount
			if (count($questions)) {
				$total_amount = 0;
				foreach ($questions as $key_questions => $question) {
					$question->amount = FilterParticipant::DefaultFilter($question->id_answer,$request);
					$total_amount += $question->amount;
				}

				// Count index label percentage
				foreach ($questions as $key_questions => $question) {
					$question->indexlabel = !$total_amount ? 0 : round(($question->amount / $total_amount) * 100,2);
				}
			}

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
					answers.id  as id_answer,
					answers.answer as answer,
					participants.id as participant_id,
					colors.color,
					cycles.id  as id_cycle,
					cycles.id  as cycle_type,
					cycles.name as cycle,
					0 AS amount,
					0 AS indexlabel'
					)
				)
				->join('question_categories','questions.question_category_id','=','question_categories.id')
				->join('answers','answers.question_id','=','questions.id')
				->join('colors','answers.color_id','=','colors.id')
				->join('question_participants','question_participants.participant_id','=','answers.id')
				->join('participants','participants.id','=','question_participants.participant_id')
				->join('regions','regions.id','=','participants.region_id')
				->join('cycles','cycles.id','=','participants.cycle_id')
				->join('filter_participants','filter_participants.participant_id','=','participants.id')
				;

			if (count($request)) {
				if (!empty($request['region'])) {
					$questions =  $questions->where('regions.name', '=', (string)$request['region']);
				}
				if (!empty($request['category'])) {
					$questions =  $questions->where('question_categories.id', '=', $request['category']);
				}
				if (!empty($request['question'])) {
					$questions =  $questions->where('questions.id', '=', $request['question']);
				}
			}

			$questions =  $questions
				->groupBy('answer')
				->get();

		// Count question amount
		$total_amount = 0;
		foreach ($questions as $key_questions => $question) {
			$question->amount = FilterParticipant::DefaultFilter($question->id_answer);
			$total_amount += $question->amount;
		}

		// Count index label percentage
		foreach ($questions as $key_questions => $question) {
			$question->indexlabel = !$total_amount ? 0 : round(($question->amount / $total_amount) * 100,2);
		}

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
					0 AS amount,
					0 AS indexlabel'
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
					$questions =  $questions->where('questions.id', '=', $request['question']);
				}
			}

			$questions =  $questions
			->groupBy('answer')
			->get();

			// Count question amount
			$total_amount = 0;
			foreach ($questions as $key_questions => $question) {
				$question->amount = FilterParticipant::FilterOptions($question->id_answer,$request);
				$total_amount += $question->amount;
			}

			// Count index label percentage
			foreach ($questions as $key_questions => $question) {
				$question->indexlabel = round(!$total_amount ? 0 : ($question->amount / $total_amount) * 100,2);
			}

		return $questions;
	}

	public static function CompareCycle($request = array())
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
					cycles.id  as id_cycle,
					cycles.cycle_type  as cycle_type,
					cycles.name as cycle,
					0 AS amount,
					0 AS indexlabel'
					)
				)
				->join('question_categories','questions.question_category_id','=','question_categories.id')
				->join('answers','answers.question_id','=','questions.id')
				->join('colors','answers.color_id','=','colors.id')
				->join('question_participants','question_participants.participant_id','=','answers.id')
				->join('participants','participants.id','=','question_participants.participant_id')
				->join('regions','regions.id','=','participants.region_id')
				->join('cycles','cycles.id','=','participants.cycle_id')
				->join('filter_participants','filter_participants.participant_id','=','participants.id')
				;

			if (count($request)) {
				if (!empty($request['region'])) {
					$questions =  $questions->where('regions.name', '=', (string)$request['region']);
				}
				if (!empty($request['category'])) {
					$questions =  $questions->where('question_categories.id', '=', $request['category']);
				}
				if (!empty($request['question'])) {
					$questions =  $questions->where('questions.id', '=', $request['question']);
				}
			}

			$questions =  $questions
				->groupBy('answer')
				->get();

		if (count($questions)) {
			// Count question amount
			$compare_questions = array();

			foreach ($questions as $key_question => $question) {

				$compare_questions['baseline'][$question->answer] = new stdClass();
				$compare_questions['baseline'][$question->answer]->answer_id = $question->id_answer;
				$compare_questions['baseline'][$question->answer]->cycle = $question->cycle;
				$compare_questions['baseline'][$question->answer]->answer = $question->answer;
				$compare_questions['baseline'][$question->answer]->color = $question->color;
				list($compare_questions['baseline'][$question->answer]->amount,
					$compare_questions['baseline'][$question->answer]->cycle,
					$compare_questions['baseline'][$question->answer]->cycle_type
					)
					 = FilterParticipant::CompareQuestion($question->id_answer,0);

				$compare_questions['endline'][$question->answer] = new stdClass();
				$compare_questions['endline'][$question->answer]->answer_id = $question->id_answer;
				$compare_questions['endline'][$question->answer]->cycle = $question->cycle;
				$compare_questions['endline'][$question->answer]->answer = $question->answer;
				$compare_questions['endline'][$question->answer]->color = $question->color;
				list($compare_questions['baseline'][$question->answer]->amount,
					$compare_questions['endline'][$question->answer]->cycle,
					$compare_questions['endline'][$question->answer]->cycle_type
					)
					 = FilterParticipant::CompareQuestion($question->id_answer,1);
			}
		}

		return $compare_questions;
	}
}