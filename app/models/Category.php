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
		'code_id'
		);
	protected $guarded = array('id');

	/* Rules */
	public static $rules = array(
		'name' => 'required',
		'code_id' => 'required',
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
}