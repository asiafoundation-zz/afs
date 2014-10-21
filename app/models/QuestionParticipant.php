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

	public static function DefaultQuestion($answer_id,$request = array())
	{
		$filter_queries =  DB::table('question_participants')
			->select(
				DB::raw(
					'participants.id as id_participant,
					filter_participants.category_item_id as id_category_item'
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
}