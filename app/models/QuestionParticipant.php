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

	public function participant()
	{
		return $this->belongsTo('Participant');
	}

	public static function checkData($answer_id,$participant_id,$region_id,$sample_type)
	{
		$question_participant = QuestionParticipant::where('answer_id', '=', $answer_id)
			->where('participant_id', '=', $participant_id)
			->where('region_id', '=', $region_id)
			->where('sample_type', '=', $sample_type)
			->first();

		if(!isset($question_participant))
		{
			$question_participant = QuestionParticipant::create(array('answer_id' => $answer_id,'participant_id' => $participant_id,'region_id' => $region_id,'sample_type' => $sample_type));

			$amount = Amount::checkData($answer_id, $region_id, $sample_type);
		}
		return $question_participant;
	}

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
		foreach ($default_questions as $key_default_question => $default_question) {
			$region_queries[$key_default_question] =  DB::table('question_participants')
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
		}
		
		$regions_array = array();
		$regions = array();
		if (count($region_queries)) {
			// Compare
			foreach ($region_queries as $key => $region_query_lists) {
				foreach ($region_query_lists as $key_region_queries => $region_query) {
					$regions_array[$region_query->id_region]["region_id"] = $region_query->id_region;
					$regions_array[$region_query->id_region]["name"] = $region_query->name[0] == " " ? substr($region_query->name,1) : $region_query->name;

					if (empty($regions_array[$region_query->id_region]["amount"])) {
						$regions_array[$region_query->id_region]["answer_name"] = $region_query->answer_name;
						$regions_array[$region_query->id_region]["answer_id"] = $region_query->id_answer;
						$regions_array[$region_query->id_region]["amount"] = $region_query->amount;
						$regions_array[$region_query->id_region]["color"] = $region_query->color;
					}
					if ((int)$region_query->amount > (int)$regions_array[$region_query->id_region]["amount"] 
						and $region_query->id_region  == $regions_array[$region_query->id_region]["region_id"] ) {
						$regions_array[$region_query->id_region]["answer_name"] = $region_query->answer_name;
						$regions_array[$region_query->id_region]["answer_id"] = $region_query->id_answer;
						$regions_array[$region_query->id_region]["amount"] = $region_query->amount;
						$regions_array[$region_query->id_region]["color"] = $region_query->color;
					}
				}
			}
			// Sort arry with number
			$i=0;
			foreach ($regions_array as $key_regions_array => $value) {
				$regions[$i] = $value;
				$i++;
			}
		}
		return $regions;
	}
}