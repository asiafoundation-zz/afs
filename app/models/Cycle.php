<?php
class Cycle extends Eloquent {

	/* Soft Delete */
	protected $softDelete = true;

	/* Eloquent */
	public $table = "cycles";
	public $timestamps = true;

	/* Disabled Basic Actions */
	public static $disabledActions = array();

	/* Route */
	public $route = 'cycle';

	/* Mass Assignment */
	protected $fillable = array(
		'name',
		'excel_file',
		'cycle_type',
		'display_name',
		'survey_id'
		);
	protected $guarded = array('id');

	/* Rules */
	public static $rules = array(
		'name' => 'required',
		);

	/* Database Structure */
	public static function structure()
	{
		$fields = array(
			'name' => array(
				'type' => 'text',
				'onIndex' => true
			)
		);

		return compact('fields');
	}

	public static function QuestionCycle($default_question)
	{
		$question_cycle =  DB::table('cycles')
			->select(
				'cycles.id',
				'name',
				'cycle_type'
				)
			->join('answers','answers.cycle_id','=','cycles.id')
			->join('questions','questions.id','=','answers.question_id')
			->where('questions.id', '=',$default_question->id_question)
			->GroupBy('name')
			->orderBy('cycle_type', 'asc')
			->get();

			return $question_cycle;
	}

	public static function AllCycle($survey_id)
	{
		$question_cycle =  DB::table('cycles')
			->select(
				'cycles.id',
				'cycles.name',
				'cycles.cycle_type',
				'cycles.display_name'
				)
			->join('answers','answers.cycle_id','=','cycles.id')
			->join('questions','questions.id','=','answers.question_id')
			->join('question_categories','question_categories.id','=','questions.question_category_id')
			->where('cycles.survey_id', '=',$survey_id)
			->GroupBy('name')
			->orderBy('cycle_type', 'asc')
			->get();

			return $question_cycle;
	}

	public static function checkData($data,$cycle_type)
	{
		$cycle = Cycle::where('name', '=', $data)->where('cycle_type','=',$cycle_type)->first();
		
		if(is_null($cycle))
		{
			$cycle = Cycle::create(array('name' => $data,'cycle_type'=>$cycle_type));
		}
		$cycle_id = $cycle->id;
		return $cycle_id;
	}
}