<?php
class MasterCode extends Eloquent {

	/* Soft Delete 
	use SoftDeletingTrait;
	protected $dates = ['deleted_at'];
	*/

	/* Eloquent */
	public $table = "master_codes";
	public $timestamps = true;

	public static $formItem = "codes";

	/* Disabled Basic Actions */
	public static $disabledActions = array();

	/* Route */
	public $route = 'master_codes';

	/* Mass Assignment */
	protected $fillable = array(
		'master_code',
		'attribute_code',
		'survey_id'
		);
	protected $guarded = array('id');

	/* Rules */
	public static $rules = array(
		'master_code' => 'required',
		'attribute_code' => 'required',
		'survey_id' => 'required'
		);

	/* Database Structure */
	public static function structure()
	{
		$fields = array(
			'master_code' => array(
			'type' => 'text',
			'onIndex' => true
			),
			'attribute_code' => array(
			'type' => 'number',
			'onIndex' => true
			),
			'survey_id' => array(
			'type' => 'number',
			'onIndex' => true
			)
		);

		return compact('fields');
	}

	public function codes()
	{
		return $this->hasMany('Code');
	}

	public static function savingProcess($survey,$request=array())
	{
		$status = true;
		// try{
		// 	DB::beginTransaction();
			$options_selected = $request;
			
			$options_selected = array(
				'category' => $options_selected['category'],
				'category_question' => !empty($options_selected['category_question']) ? $options_selected['category_question'] : "",
				'code' => !empty($options_selected['code']) ? $options_selected['code'] : "",
				'label' => !empty($options_selected['label']) ? $options_selected['label'] : "",
				);
			
			$code_labels = explode('_', $options_selected['code']);
			$master_code_label = reset($code_labels);
			$code_label = !empty($code_labels[1]) ? $code_labels[1] : "";

			// Saving
			$master_codes_data = DB::table('master_codes')->where('master_code','=',$master_code_label)->first();

			

			if (!isset($master_codes_data)) {
				$master_code = new MasterCode;
				$master_code->master_code = $master_code_label;
				$master_code->survey_id = $survey->id;
				$master_code->save();

				$master_codes_data = $master_code;
			}
			
			$code = new Code;
			$code->code = $code_label;
			$code->type = $options_selected['category'];
			$code->master_code_id = $master_codes_data->id;
			$code->save();

			// Save question and question category
			if ($options_selected['category'] == 3) {
				$category = Category::checkData($options_selected['code'],$code->id,$survey->id);
			}
			if ($options_selected['category'] == 4) {
				// Save category question 
				Log::info($options_selected['category_question']);
				$question_category = QuestionCategory::checkData($options_selected['category_question'],$code->id,$survey->id);
				// Save question
				$question = Question::checkData($options_selected['label'],$code->id,$question_category->id);
			}

		// 	DB::commit();
		// 	$status = true;
		// }
		// catch(\PDOException $e){
  //     DB::rollback();
  //     $status = false;
  //   }
		return $status;
	}
	
	public static function loadData($survey_id)
	{
		$list_data = DB::table('master_codes')
			->select(
				'master_codes.id as master_code_id',
				'master_codes.master_code as master_code',
				'codes.id as code_id',
				'codes.code as code_name',
				'codes.type as type'
				)
			->join('codes','codes.master_code_id','=','master_codes.id')
			->where('master_codes.survey_id', '=', $survey_id)
			->get();

		$data = array();
		if (count($list_data)) {
			foreach ($list_data as $key_list_data => $single_data) {
				$code = !empty($single_data->code_name) ? $single_data->master_code."_".$single_data->code_name : $single_data->master_code;

				$data[strtolower($code)]['master_code_id'] = $single_data->master_code_id;
				$data[strtolower($code)]['master_code'] = $single_data->master_code;
				$data[strtolower($code)]['code_id'] = $single_data->code_id;
				$data[strtolower($code)]['code_name'] = $single_data->code_name;
				$data[strtolower($code)]['code'] = $code;
				$data[strtolower($code)]['type'] = $single_data->type;
			}
		}

		return $data;
	}
}
