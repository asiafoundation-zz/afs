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
		'answer_id',
		'participant_id',
		'region_id'
		);
	protected $guarded = array('id');

	/* Rules */
	public static $rules = array(
		'answer_id' => 'required|numeric',
		'participant_id' => 'required|numeric',
		'region_id' => 'required|numeric'
		);

	/* Database Structure */
	public static function structure()
	{
		$fields = array(
			'answer_id' => array(
				'type' => 'number',
				'onIndex' => true
			),
			'participant_id' => array(
				'type' => 'number',
				'onIndex' => true
			),
			'region_id' => array(
				'type' => 'number',
				'onIndex' => true
			),
		);

		return compact('fields');
	}

// Will be removed
	// public static function DefaultQuestion($answer_id,$request = array())
	// {
	// 	$filter_queries =  DB::table('question_participants')
	// 		->select(
	// 			DB::raw(
	// 				'participants.id as id_participant,
	// 				regions.name,
	// 				question_participants.id as question_participants'
	// 				)
	// 			)
	// 		->join('participants','participants.id','=','question_participants.participant_id')
	// 		->leftjoin('regions','regions.id','=','question_participants.region_id')
	// 		->where('question_participants.answer_id', '=',$answer_id);

	// 		if (!empty($request['region'])) {
	// 			$filter_queries =  $filter_queries->where('regions.name', '=', (string)$request['region']);
	// 		}

	// 		$filter_queries =  $filter_queries->GroupBy('id_participant')
	// 		->get();

	// 		$data_result = count($filter_queries);

	// 		return $data_result;
	// }

	public static function CompareQuestion($answer_id,$cycle_type)
	{
		$filter_queries =  DB::table('question_participants')
			->select(
				DB::raw(
					'participants.id as id_participant'
					)
				)
			->join('participants','participants.id','=','question_participants.participant_id')
			->join('regions','regions.id','=','question_participants.region_id')
			->join('filter_participants','filter_participants.participant_id','=','participants.id')
			->where('question_participants.answer_id', '=',$answer_id)
			->GroupBy('id_participant')
			->get();

			$data_result = count($filter_queries);

			return $data_result;
	}

	public static function RegionColor($cycle_id,$default_questions)
	{
		$regions = array();
		foreach ($default_questions as $key_default_question => $default_question) {
			$region_queries =  DB::table('question_participants')
				->select(
					DB::raw(
						'regions.id as id_region,
						answers.id as id_answer,
						answers.answer as answer_name,
						question_participants.id as question_participant_id,
						regions.name,
						colors.color as color,
						(SELECT count(id) from question_participants where question_participants.region_id = id_region and question_participants.answer_id = id_answer) AS amount'
						)
				)
				->join('regions','regions.id','=','question_participants.region_id')
				->join('answers','answers.id','=','question_participants.answer_id')
				->join('colors','colors.id','=','answers.color_id')
				->where('question_participants.answer_id','=',$default_question->id_answer)
				->GroupBy('id_region')
				->GroupBy('id_answer')
				->get();

			if (count($region_queries)) {
				foreach ($region_queries as $key_region_queries => $region_query) {
					$regions[$key_region_queries]["region_id"] = $region_query->id_region;
					$regions[$key_region_queries]["name"] = $region_query->name;

					if (empty($regions[$key_region_queries]["amount"])) {
						$regions[$key_region_queries]["answer_name"] = $region_query->answer_name;
						$regions[$key_region_queries]["answer_id"] = $region_query->id_answer;
						$regions[$key_region_queries]["amount"] = $region_query->amount;
						$regions[$key_region_queries]["color"] = $region_query->color;
					}
					if ((int)$region_query->amount > (int)$regions[$key_region_queries]["amount"]) {
						$regions[$key_region_queries]["answer_name"] = $region_query->answer_name;
						$regions[$key_region_queries]["answer_id"] = $region_query->id_answer;
						$regions[$key_region_queries]["amount"] = $region_query->amount;
						$regions[$key_region_queries]["color"] = $region_query->color;
					}
				}
			}
		}

		return $regions;
	}
}