<?php
class Region extends Eloquent {

	/* Soft Delete 
	use SoftDeletingTrait;
	protected $dates = ['deleted_at'];
	*/
	/* Eloquent */
	public $table = "regions";
	public $timestamps = true;


	/* Disabled Basic Actions */
	public static $disabledActions = array();

	/* Route */
	public $route = 'region';

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
			)
			);

		return compact('fields');
	}

	public static function RegionColor()
	{
		$region_queries =  DB::table('regions')
			->select(
				DB::raw(
					'regions.id as region_id,
					answers.id as id_answer,
					answers.answer as answer_name,
					regions.name,
					colors.color as color,
					0 AS amount'
					)
			)
			->join('question_participants','question_participants.region_id','=','regions.id')
			->join('answers','answers.id','=','question_participants.answer_id')
			->join('colors','colors.id','=','answers.color_id')
			->get();

print '<pre>';
print_r($region_queries);
print '<pre>';
exit();
			/*
			 * Get regions with maximum values votes
			 */
			$regions = array();
			if (count($region_queries)) {
				foreach ($region_queries as $key_region_queries => $region_query) {
					$regions[$region_query->name]["region_id"] = $region_query->region_id;
					$regions[$region_query->name]["name"] = $region_query->name;
					$regions[$region_query->name]["answer_name"] = $region_query->answer_name;

					if (empty($regions[$region_query->name]["amount"])) {
						$regions[$region_query->name]["answer_id"] = $region_query->answer_id;
						$regions[$region_query->name]["amount"] = $region_query->amount;
						$regions[$region_query->name]["color"] = $region_query->color;
					}
					if ($region_query->amount > $regions[$region_query->name]["amount"]) {
						$regions[$region_query->name]["answer_id"] = $region_query->answer_id;
						$regions[$region_query->name]["amount"] = $region_query->amount;
						$regions[$region_query->name]["color"] = $region_query->color;
					}
				}
			}

		return $regions;
	}

	public static function QuestionCategoryFilterRegion($request = array())
	{
		$regions =  DB::table('regions')
			->select(
				DB::raw(
					'question_categories.id as id_question_categories,
					question_categories.name as question_categories,
					questions.id as id_question,
					questions.question as question'
					)
				)
			->join('questioners','questioners.region_id','=','regions.id')
			->join('answers','answers.id','=','questioners.answer_id')
			->join('questions','questions.id','=','answers.question_id')
			->join('question_categories','questions.id','=','questions.id');

			if (count($request)) {
				if (isset($request['region'])) {
					$regions =  $regions->where('regions.name', '=', $request['region']);
				}
			}

			$regions =  $regions
			->GroupBy('id_question_categories')
			->GroupBy('id_question')
			->get();

		return $regions;
	}

	public static function SplitQuestionsCategory($question_categories)
	{
		$split_data = array();

		if (count($question_categories)) {
			foreach ($question_categories as $key_question_categories => $question_category) {
				$split_data['question_lists'][$key_question_categories] = new stdClass;
				$split_data['question_lists'][$key_question_categories]->id = $question_category->id_question;
				$split_data['question_lists'][$key_question_categories]->question = $question_category->question;

				$split_data['question_categories'][$question_category->id_question_categories] = new stdClass;
				$split_data['question_categories'][$question_category->id_question_categories]->id = $question_category->id_question_categories;
				$split_data['question_categories'][$question_category->id_question_categories]->name = $question_category->question_categories;

			}
		}
		return $split_data;
	}
}