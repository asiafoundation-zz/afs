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
					(select amount_filters.amount from amount_filters where answer_id = '. $request['answer_id'] .' and category_item_id = id_category_item) as amount,
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
		// If Category Empty
		$data = !empty($data) ? $data : 'Other';

		$category = Category::where('name', '=', $data)->first();
		if(!isset($category))
		{
			$category = Category::create(array('name' => $data, 'display_name' => $data,'code_id' => $code_id, 'survey_id' => $survey_id, 'is_active' => 1));
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
				'question_participants.participant_id as participant_id',
				'categories.id as category_id',
				'categories.name as category',
				'category_items.id as category_item_id',
				'category_items.name as category_item',
				'filter_participants.participant_id'
				)
			->join('answers','answers.question_id','=','questions.id')
			->join('question_participants','question_participants.answer_id','=','answers.id')
			->join('participants','participants.id','=','question_participants.participant_id')
			->join('filter_participants','filter_participants.participant_id','=','participants.id')
			->join('category_items','category_items.id','=','filter_participants.category_item_id')
			->join('categories','categories.id','=','category_items.category_id')
			->where('questions.id','=',$request['question_id'])
			->where('question_participants.sample_type','=',0)
			->where('answers.cycle_id','=',$request['cycle_id'])
			->where('categories.survey_id','=',$request['survey_id'])
			->get();

		$data_query_headers=DB::table('categories')
			->select(
					'categories.id as category_id',
					'categories.name as category',
					'category_items.id as category_item_id',
					'category_items.name as category_item'
					)
			->join('category_items','category_items.category_id','=','categories.id')
			->where('categories.survey_id','=',$request['survey_id'])
			->get();

		// Arrange Headers for table
		$data_headers = array();
		foreach ($data_query_headers as $key_data_headers => $data_query_header) {
			$data_headers[$data_query_header->category_id]['category_id'] = $data_query_header->category_id;
			$data_headers[$data_query_header->category_id]['category'] = $data_query_header->category;
			$data_headers[$data_query_header->category_id]['colspan'] = empty($data_headers[$data_query_header->category_id]['colspan']) ? 1 : count($data_headers[$data_query_header->category_id]['category_items']) + 1 ;

			$data_headers[$data_query_header->category_id]['category_items'][$data_query_header->category_item_id]['category_item_id'] = $data_query_header->category_item_id;
			$data_headers[$data_query_header->category_id]['category_items'][$data_query_header->category_item_id]['category_item'] = $data_query_header->category_item;
		}

			// Arrange List data for table
		$datas=array();
		foreach ($get_answers as $key_get_answers => $get_answer) {
			$datas[$get_answer->answer_id]['answer_id'] = $get_answer->answer_id;
			$datas[$get_answer->answer_id]['answer'] = $get_answer->answer;

			$datas[$get_answer->answer_id]['category_id'] = $get_answer->category_id;

			// To count participant choose certain answer with certain category
			$count = isset($datas[$get_answer->answer_id]['answer_category'][$get_answer->category_item_id]['count']) ? $datas[$get_answer->answer_id]['answer_category'][$get_answer->category_item_id]['count'] : 0;

			$datas[$get_answer->answer_id]['answer_category'][$get_answer->category_item_id]['count'] = $count + 1;
			$datas[$get_answer->answer_id]['answer_category'][$get_answer->category_item_id]['category_item_id'] = $get_answer->category_item_id;
		}

		/*
		 * Built table header
		 */
		$table_data_category = '<tr><td rowspan="2">Answer</td>';
		$table_data_category_item = '<tr>';

		foreach ($data_headers as $key_data_headers => $data_header) {
			$table_data_category .= '<td  align="center" colspan='.$data_header['colspan'].'>'.strtoupper($data_header['category']).'</td>';

			foreach ($data_header['category_items'] as $key_category_items => $category_item) {
				$table_data_category_item .= '<td>'.strtoupper($category_item['category_item']).'</td>';
			}
		}

		$table_data_category .= '</tr>';
		$table_data_category_item .= '</tr>';

		$table_header = '<thead>'.$table_data_category.$table_data_category_item.'</thead>';
		
		/*
		 *  End Built table header
		 */

		/*
		 * Built table body
		 */
		$table_data_answer = '';
		foreach ($datas as $key_datas => $single_data) {
			$table_data_answer .= '<tr>';
			$table_data_answer .= '<td>'.$single_data['answer'].'</td>';

			foreach ($data_headers as $key_data_headers => $data_header) {

				foreach ($data_header['category_items'] as $key_category_items => $category_item) {
					if (!empty($single_data['answer_category'][$category_item['category_item_id']])) {
						if ($category_item['category_item_id'] == $single_data['answer_category'][$category_item['category_item_id']]['category_item_id']) {
							$table_data_answer .= '<td>'.$single_data['answer_category'][$category_item['category_item_id']]['count'].'</td>';
						}
					}else{
						$table_data_answer .= '<td>0</td>';
					}
				}

			}

			$table_data_answer .= '</tr>';
		}
		/*
		 *  End Built table body
		 */

		$table_body = '<tbody>'.$table_data_answer.'</tbody>';
		$table_data = $table_header.$table_body;

		return $table_data;
	}
}