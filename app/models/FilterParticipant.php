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

	public static function FilterOptions($answer_id,$request = array())
	{
		$option_filters = "";

		if (count($request['option_filters'])) {
			$option_filters_trim = rtrim($request['option_filters'],",");
			$option_filters_array = explode(",", $option_filters_trim);

			// $option_filters .=  "and (";
			// foreach ($option_filters_array as $key => $option_filters_array_single) {
			// 	if ($key == count($option_filters_array) - 1) {
			// 		$option_filters .= " or ";
			// 	}
			// 	$option_filters .= "filter_participants.category_item_id = ".$option_filters_array_single;
			// }
			// $option_filters .=  ") ";

			$option_filters .= " and filter_participants.category_item_id IN (".(string)$option_filters_trim.") ";
		}

		$filter_queries =  DB::table('filter_participants')
			->select(
				DB::raw(
					'participants.id as id_participant,
					filter_participants.category_item_id as id_category_item'
					)
				)
			->join('participants','participants.id','=','filter_participants.participant_id')
			->join('regions','regions.id','=','participants.region_id')
			->join('question_participants','question_participants.participant_id','=','participants.id')
			->where('question_participants.answer_id', '=',$answer_id)
			;

			if ($request['region'] != "null") {
				$filter_queries = $filter_queries->where('regions.name', '=',$request['region']);
			}

			if ($request['cycle']) {
				$filter_queries = $filter_queries->where('participants.cycle_id', '=',$request['cycle']);
			}

			$filter_queries = $filter_queries
				->whereIn('filter_participants.category_item_id',$option_filters_array)
				->get();

			$data_merge = array();
			$data_result = 0;
			if (count($filter_queries)) {
				foreach ($filter_queries as $key_filter_queries => $filter_query) {

					// Grouping query result according it's population
					if(empty($data_merge[$filter_query->id_participant])){
						$data_merge[$filter_query->id_participant] = 0;
					}
					$data_merge[$filter_query->id_participant]++;

					// Compare valid category item with total valid category item 
					if(count($option_filters_array) == $data_merge[$filter_query->id_participant]){
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
}