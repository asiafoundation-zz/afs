<?php
class Category extends Eloquent {

	/* Soft Delete */
	use SoftDeletingTrait;
	protected $dates = ['deleted_at'];

	/* Eloquent */
	public $table = "categories";
	public $timestamps = true;

	public static $formItem = "category_items";

	public function category_items()
	{
		return $this->hasMany('CategoryItem');
	}
				
	/* Disabled Basic Actions */
	public static $disabledActions = array();

	/* Route */
	public $route = 'category';

	/* Mass Assignment */
	protected $fillable = array(
		'name',
		'code_id',
		'survey_id',
		'is_active'
		);
	protected $guarded = array('id');

	/* Rules */
	public static $rules = array(
		'name' => 'required',
		'code_id' => 'required',
		'survey_id' => 'required',
		'is_active' => 'required'
		);

	/* Database Structure */
	public static function structure()
	{
		$fields = array(
			'name' => array(
				'type' => 'text',
				'onIndex' => true
			),
			'code_id' => array(
				'type' => 'number',
				'onIndex' => true
			),
			'survey_id' => array(
				'type' => 'number',
				'onIndex' => true
			),
			'is_active' => array(
				'type' => 'number',
				'onIndex' => true
			),
		);

		return compact('fields');
	}

	public static function DetailParticipant($request)
	{
		// If first load
		if (($request['FilterMove'] == 0)) {
			$request['category_filter'] =  DB::table('categories')->select('id')->first();
			$request['category_filter'] = $request['category_filter']->id;
		}
		// If Backward
		if (($request['FilterMove'] == 1)) {
			$request['category_filter'] =  DB::table('categories')->select('id')->whereRaw("categories.id = (select max(id) from categories where categories.id < ".$request['category_filter'].")")->first();

			// If no backward
			if (!count($request['category_filter'])) {
				$request['category_filter'] =  DB::table('categories')->select('id')->orderBy('id', 'desc')->first();
			}
			$request['category_filter'] = $request['category_filter']->id;
		}
		// If Forward
		if (($request['FilterMove'] == 2)) {
			$request['category_filter'] =  DB::table('categories')->select('id')->whereRaw("categories.id = (select min(id) from categories where categories.id > ".$request['category_filter'].")")->first();

			// If no forward
			if (!count($request['category_filter'])) {
				$request['category_filter'] =  DB::table('categories')->select('id')->first();
			}
			$request['category_filter'] = $request['category_filter']->id;
		}

		$filter_queries =  DB::table('categories')
			->select(
				DB::raw(
					'categories.id as id_category,
					categories.name as category_name,
					category_items.id as id_category_item,
					category_items.name as category_item_name,
					(SELECT count(participants.id) from participants JOIN question_participants ON question_participants.participant_id = participants.id JOIN filter_participants ON filter_participants.participant_id = participants.id WHERE filter_participants.category_item_id = id_category_item AND question_participants.answer_id = '.$request['answer_id'].' ) AS amount,
					0 AS indexlabel'
					)
				)
			->join('category_items','category_items.category_id','=','categories.id')
			->where('categories.id','=',$request['category_filter'])
			->get();

			// Count IndexLabel Percentage
			$total_amount = 0;
			if (count($filter_queries)) {
				foreach ($filter_queries as $key_filter_queries => $filter_query) {
					$total_amount += $filter_query->amount;
				}

				foreach ($filter_queries as $key_filter_queries => $filter_query) {
					$filter_query->indexlabel = round(!$total_amount ? 0 : ($filter_query->amount / $total_amount) * 100,2);
				}
			}

			return $filter_queries;
	}
	public static function checkData($data,$code_id,$survey_id)
	{
		$category = Category::where('name', '=', $data)->first();
		if(!isset($category))
		{
			$category = Category::create(array('name' => $data, 'code_id' => $code_id, 'survey_id' => $survey_id));
		}	
		return $category;
	}
	public static function getDataList($request=array())
	{
		$get_answers = DB::table('questions')
			->select(
				'questions.id as question_id',
				'answers.id as answer_id',
				'answers.answer as answer',
				'question_participants.participant_id as participant_id'
				)
			->join('answers','answers.question_id','=','questions.id')
			->join('question_participants','question_participants.answer_id','=','answers.id')
			->where('questions.id','=',$request['question_id'])
			->get();

		$get_participants = DB::table('categories')
			->select(
				'categories.id as category_id',
				'categories.name as category',
				'category_items.id as category_item_id',
				'category_items.name as category_item',
				'filter_participants.participant_id'
				)
			->join('category_items','category_items.category_id','=','categories.id')
			->join('filter_participants','filter_participants.category_item_id','=','category_items.id')
			->where('categories.survey_id','=',$request['survey_id'])
			// ->groupBy('category_item')
			->get();

			// Combine data
		$data=array();
		$data_header=array();
		foreach ($get_answers as $key_get_answers => $get_answer) {
			$data_header['answer_name'] = $get_answer->answer;
			$data[$get_answer->answer_id]['answer_name'] = $get_answer->answer;

			foreach ($get_participants as $key_get_participants => $get_participant) {
				if ($get_answer->participant_id == $get_participant->participant_id) {
					// Set Header
					$data_header['answer_category'][$get_participant->category_id]['category_name'] = strtoupper($get_participant->category);
					$data_header['answer_category'][$get_participant->category_id]['categories'][$get_participant->category_item_id] = $get_participant->category_item;

					// If empty
					$count = empty($data[$get_answer->answer_id]['answer_category'][$get_participant->category_item_id]) ? 0 : $data[$get_answer->answer_id]['answer_category'][$get_participant->category_item_id];

					$data[$get_answer->answer_id]['answer_category'][$get_participant->category_item_id] = $count + 1;
				}
			}
		}

		$table_data = '<thead><tr>';
		$table_data = '<thead><tr><td rowspan="2"></td>';

		$table_data_category_item = '';
		foreach ($data_header['answer_category'] as $key_data_header => $data_header_singles) {
			// $table_data .= '<td>'.$data_header_singles['category_name'].'</td>';
			foreach ($data_header_singles['categories'] as $key_data_header_single => $data_header_single) {
				
				$table_data_category_item .= '<td>'.$data_header_single.'</td>';
			}
		}
		$table_data .= '</tr></thead><tbody><tr>';

		foreach ($data as $key_data => $single_data) {
			$table_data .= '<td>'.$single_data['answer_name'].'</td>';
			foreach ($single_data['answer_category'] as $key_answer_category => $answer_category) {
				if ($key_answer_category == $data_header_singles['categories'][$key_answer_category]) {
						$table_data .= '<td>'.$answer_category.'</td>';
					}
			}
		}
		$table_data .= '</tr><tbody>';

		return $table_data;
	}
}