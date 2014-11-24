<?php
class FilterParticipant extends Eloquent {

	/* Soft Delete */
	protected $softDelete = true;

	/* Eloquent */
	public $table = "filter_participants";
	public $timestamps = true;


	/* Disabled Basic Actions */
	public static $disabledActions = array();

	/* Route */
	public $route = 'filter_participant';

	/* Mass Assignment */
	protected $fillable = array(
		'category_item_id',
		'participant_id'
		);
	protected $guarded = array('id');

	/* Rules */
	public static $rules = array(
		'category_item_id' => 'required|numeric',
		'participant_id' => 'required|numeric'
		);

	/* Database Structure */
	public static function structure()
	{
		$fields = array(
			'category_item_id' => array(
				'type' => 'number',
				'onIndex' => true
			),
			'participant_id' => array(
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

	public static function FilterOptions($answer_id,$request = array())
	{
		$option_filters = "";

		if (count($request['option_filters'])) {
			$option_filters_trim = rtrim($request['option_filters'],",");
			$option_filters_array = explode(",", $option_filters_trim);
		}

		$filter_queries =  DB::table('filter_participants')
			->select(
				DB::raw(
					'count(participants.id) as count_participant,
					filter_participants.category_item_id as id_category_item'
					)
				)
			->join('participants','participants.id','=','filter_participants.participant_id')
			->join('question_participants','question_participants.participant_id','=','participants.id')
			->join('regions','regions.id','=','question_participants.region_id')
			->where('question_participants.answer_id', '=',$answer_id)
			;

			if (!empty($request['region'])) {
					$region = $request['region'];
					$region_dapil = $request['region_dapil'];
					$questions =  $questions->where(
						function ($query) use ($region,$region_dapil) {
						$query->where('regions.name', '=', (string)$region)
						->orWhere('regions.name', '=', (string)$region_dapil);
					});
				}

			$filter_queries = $filter_queries
				// ->whereIn('filter_participants.category_item_id',$option_filters_array)
				->whereIn('filter_participants.category_item_id',$option_filters_array)
				->groupBy('participants.id')
				->get();
				
			$data_merge = array();
			$data_result = 0;
			if (count($filter_queries)) {
				foreach ($filter_queries as $key_filter_queries => $filter_query) {
					if(count($option_filters_array) == $filter_query->count_participant){
						$data_result++;
					}
				}
			}
			else
			{
				$data_result = 0;
			}
			return $data_result;
	}

	public static function CompareQuestion($answer_id,$cycle_type)
	{
		$cycle_name = "";
		$filter_queries =  DB::table('filter_participants')
			->select(
				DB::raw(
					'cycles.name as cycle,
					cycles.cycle_type as cycle_type,
					participants.id as id_participant,
					filter_participants.category_item_id as id_category_item'
					)
				)
			->join('participants','participants.id','=','filter_participants.participant_id')
			->join('regions','regions.id','=','participants.region_id')
			->join('cycles','cycles.id','=','participants.cycle_id')
			->join('question_participants','question_participants.participant_id','=','participants.id')
			->where('question_participants.answer_id', '=',$answer_id)
			->where('cycles.cycle_type', '=',$cycle_type)
			->GroupBy('id_participant')
			->get();

			$data_result = count($filter_queries);

			if (count($filter_queries)) {
				$filter_query = reset($filter_queries);
				$cycle_name = $filter_query->cycle;
			}

			return array($data_result,$cycle_name,$cycle_type);
	}
}