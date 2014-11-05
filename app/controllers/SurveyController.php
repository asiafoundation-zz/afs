<?php

class SurveyController extends AvelcaController {

	public function __construct(\Survey $Model)
	{
		parent::__construct($Model);
	}
	
	public function getIndex()
	{
		$data['surveys'] = Survey::getSurveys();
		// Paginations
		$data['no'] = (Input::get('page') -1) * 10 +1;

		return View::make('admin.survey.index',$data);
	}

	public function reupload()
	{

		DB::table('answers')->truncate();
		DB::table('categories')->truncate();
		DB::table('category_items')->truncate();
		DB::table('codes')->truncate();
		DB::table('cycles')->truncate();
		DB::table('filter_participants')->truncate();
		DB::table('master_codes')->truncate();
		DB::table('participants')->truncate();
		DB::table('question_categories')->truncate();
		DB::table('question_participants')->truncate();
		DB::table('questions')->truncate();
		DB::table('regions')->truncate();
		DB::table('surveys')->truncate();

		Session::flash('survey_deleted', 'Survey Deleted');
		return Redirect::to('/admin/survey');
	}

	public function postIndex()
	{
		$rule = array('survey_name' => 'Required');

		$validator = Validator::make(Input::all(), $rule);

		if($validator->passes())
		{
			$survey = Survey::create(array('name' => Input::get('survey_name'), 'baseline_file' => Input::get('excel'), 'geojson_file' => Input::get('geojson'),'publish' => 1));

			if($survey)
			{
				return Redirect::to('/admin/survey/category/'. $survey->id);
			}
		}
		else
		{
			return Redirect::to('/admin/survey')->withErrors($validator)->withInput();
		}
	}

	public function postUpload(){
		ini_set("memory_limit","200M");
		
		$filename = Input::file('file')->getClientOriginalName();

		if(!file_exists($filename))
		{
			$uploaded = Input::file('file')->move('uploads/', $filename);	
		}

	    return Response::json($filename);
	}

	public function getCategory($id)
	{
		$survey = Survey::where('id', '=', $id)->first();
		
		$header = Survey::readHeader($survey->baseline_file, 'E', 0);

		$content = array("Select 'Region' Filter","Please select select 'Region' with clicking a list on the left");

		$button_value = "Next";

		return View::make('admin.survey.import')
				->with('header', $header)
				->with('content', $content)
				->with('survey', $survey)
				->with('button', $button_value)
				->with('base_header', true);
	}

	public function postCategory()
	{
		$status = 0;	
		// Load survey
		$survey = Survey::where('id', '=', Input::get('survey_id'))->first();
		
		// save code
		$codes = MasterCode::savingProcess(Input::get());
		// Load Master Code Data
		$master_code = MasterCode::loadData(Input::get());
		// Load Excel Data
		$excel_data = Survey::readHeader($survey->baseline_file, 'BZ', 1);
		// Import Data
		$status = Survey::importData($survey,$master_code,$excel_data);

		return $status;
	}
}