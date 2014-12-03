<?php
class QuestionCategory extends Eloquent {

	/* Soft Delete */
	protected $softDelete = true;

	/* Eloquent */
	public $table = "question_categories";
	public $timestamps = true;

	/* Disabled Basic Actions */
	public static $disabledActions = array();

	/* Route */
	public $route = 'question_category';

	/* Mass Assignment */
	protected $fillable = array(
		'name',
		'survey_id'
		);
	protected $guarded = array('id');

	/* Rules */
	public static $rules = array(
		'name' => 'required',
		'survey_id' => 'required'
		);

	/* Database Structure */
	public static function structure()
	{
		$fields = array(
			'name' => array(
				'type' => 'text',
				'onIndex' => true
			),
			'survey_id' => array(
					'type' => 'number',
					'onIndex' => true
			)
		);

		return compact('fields');
	}
	
	public static function checkData($data,$code_id,$survey_id)
	{
		$data = empty($data) ? "Uncategorized" : $data;
		$question_category = QuestionCategory::where('name', '=', $data)->first();
		if(!isset($question_category))
		{
			$question_category = QuestionCategory::create(array('name' => $data, 'code_id' => $code_id, 'survey_id' => $survey_id));
		}	
		return $question_category;
	}

	public static function QuestionCategoryFilterRegion($request = array())
	{
		$question_categories =  DB::table('question_categories')
			->select(
				DB::raw(
					'question_categories.id as id_question_categories,
					question_categories.name as question_categories,
					questions.id as id_question,
					questions.question as question'
					)
				)
			->join('questions','questions.question_category_id','=','question_categories.id');
			// if (!empty($request['region'])) {
			// 	$question_categories = $question_categories->where('question_categories','=',$request['region']);
			// }
			if (!empty($request['category'])) {
				$question_categories = $question_categories->where('question_categories.id','=',$request['category']);
			}
			$question_categories = $question_categories->GroupBy('id_question_categories')
			->GroupBy('id_question')
			->get();

		return $question_categories;
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