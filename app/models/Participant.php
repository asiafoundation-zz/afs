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

	public static function getParticipants($participant_loads)
	{
		$participants = array();
		foreach ($participant_loads as $key_participant_loads => $participant_load) {
			$participants[$participant_load->id]['id'] = $participant_load->id;

			$participants[$participant_load->id]['questions'] =  DB::table('participants')
			->select(
				'participants.id as participant_id',
				'participants.sample_type as sample_type',
				'regions.name as region',
				'answers.answer as answers',
				'answers.cycle_id as cycle',
				'questions.id as question_id',
				'questions.question as questions',
				'codes.code',
				'master_codes.master_code'
				)
			->join('question_participants','question_participants.participant_id','=','participants.id')
			->join('regions','regions.id','=','question_participants.region_id')
			->join('answers','answers.id','=','question_participants.answer_id')
			->join('questions','questions.id','=','answers.question_id')
			->join('codes','codes.id','=','questions.code_id')
			->join('master_codes','master_codes.id','=','codes.master_code_id')
			->where('question_participants.participant_id','=',$participant_load->id)
			->orderBy('question_id', 'asc')
			->get();

			$participants[$participant_load->id]['filters'] = DB::table('participants')
			->select(
				'participants.id as participant_id',
				'participants.sample_type as sample_type',
				'category_items.name as category_items',
				'categories.name as category',
				'categories.id as category_id',
				'codes.code',
				'master_codes.master_code'
				)
			->join('filter_participants','filter_participants.participant_id','=','participants.id')
			->join('category_items','category_items.id','=','filter_participants.category_item_id')
			->join('categories','categories.id','=','category_items.category_id')
			->join('codes','codes.id','=','categories.code_id')
			->join('master_codes','master_codes.id','=','codes.master_code_id')
			->where('filter_participants.participant_id','=',$participant_load->id)
			->orderBy('category_id', 'asc')
			->get();

			$cycle = DB::table('cycles')->where('id','=',$participants[$participant_load->id]['questions'][0]->cycle)->first();
			$participants[$participant_load->id]['region'] = $participants[$participant_load->id]['questions'][0]->region;
			$participants[$participant_load->id]['cycle'] = $cycle->name;
			$participants[$participant_load->id]['sample_type'] = $participants[$participant_load->id]['filters'][0]->sample_type;
		}
		return $participants;
	}
}