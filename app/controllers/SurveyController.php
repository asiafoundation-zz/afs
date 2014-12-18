<?php

class SurveyController extends AvelcaController {

	public function __construct(\Survey $Model)
	{
		parent::__construct($Model);
	}
	
	public function getIndex()
	{
		list(
			$data['surveys'],
			$data['is_refresh'],
			$data['category_show'],
			$data['survey_category_id']) = Survey::getSurveys();
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
		DB::table('delayed_jobs')->truncate();
		DB::table('amounts')->truncate();
		DB::table('amount_filters')->truncate();

		// Remove All File
		$path = public_path()."/uploads/";
		$files = glob($path.'*');
		foreach($files as $file){
			if(is_file($file))
			unlink($file);
		}
		Session::flash('survey_deleted', 'Survey Deleted');
		return Redirect::to('/admin/survey');
	}

	public function postIndex()
	{
		$request = Input::get();
		$files = Input::file();

		if (!empty($request['survey_id'])) {
			$survey = Survey::where('id', '=', $request['survey_id'])->first();
			if(!empty($request['geojson'])){
				$survey->geojson_file = $request['geojson'];
				$survey->save();

				self::postUpload($files);

				Session::flash('alert-class', 'alert-success');
				Session::flash('message', 'Save Succeed');

				return Redirect::to('/admin/survey/managesurvey/'. $survey->id);
			}
			elseif (!empty($request['excel'])) {
				$survey->publish = 3;
				$survey->baseline_file = self::fileRename($request['baseline_file']);
				$survey->header_file = self::fileRename($request['header_file']);
				$survey->save();

				self::postUpload($files);

				return Redirect::to('/admin/survey/category/'. $survey->id);
			}elseif (isset($request['is_default'])) {
				// Delete previous default
				$previous_default = Survey::where('is_default','=',1)->first();
				if (isset($previous_default)) {
					$previous_default->is_default = 0;
					$previous_default->save();
				}
				
				$survey->is_default = $request['is_default'];
				$survey->save();

				Session::flash('alert-class', 'alert-success');
				Session::flash('message', 'Save Succeed');

				return Redirect::to('/admin/survey/managesurvey/'. $survey->id);
			}elseif(isset($request['information'])){
				$survey->information = $request['information'];
				$survey->save();

				Session::flash('alert-class', 'alert-success');
				Session::flash('message', 'Save Succeed');
			}
		}else{
			$rule = array('survey_name' => 'Required');

			$validator = Validator::make(Input::all(), $rule);

			if($validator->passes())
			{
				$survey = Survey::create(array('name' => Input::get('survey_name'), 'baseline_file' => self::fileRename(Input::file('baseline_file')->getClientOriginalName()),'header_file' => self::fileRename(Input::file('header_file')->getClientOriginalName()), 'geojson_file' => self::fileRename(Input::file('geojson')->getClientOriginalName()),'publish' => 3));

				if($survey)
				{
					self::postUpload($files);

					// Self to delayed Job
					$delayed_jobs = DelayedJob::create(array('type' => 'parsingfile','survey_id' => $survey->id,'data' => 0,'queue' => 1));
					Session::flash('alert-class', 'alert-success');
					Session::flash('message', 'Save Succeed and Parsing File Started');
					return Redirect::to('/admin/survey');
				}
			}
			else
			{
				return Redirect::to('/admin/survey')->withErrors($validator)->withInput();
			}
		}
	}

	public function postUpload($files){
		ini_set("memory_limit","200M");
		
		foreach ($files as $key_files => $file) {
			$filename = $file->getClientOriginalName();

			if(!file_exists($filename))
			{
				$uploaded = $file->move('uploads/', $filename);	
			}
		}
		
		return Response::json($filename);
	}

	public function getCategory($id)
	{
		$survey = Survey::where('id', '=', $id)->first();

		$temporary_headers = DB::table('temporary_headers')->get();
		$header = array();
		foreach ($temporary_headers as $key_temporary_headers => $temporary_header) {
			$row = 0;
			if ($key_temporary_headers > 2) {
				foreach ($temporary_header as $key => $value) {
					$dataval = preg_replace('/[^A-Za-z0-9\-\s?\/#$%^&*()+=\-\[\];,.:<>|]\n\r/', '', $value);
					$dataval = str_replace('"', "", $dataval);
					$dataval = trim(preg_replace('/\s\s+/', ' ', $dataval));

					$header[$key_temporary_headers]['header'.$row] = $dataval;
					$row++;
				}
			}
		}
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
		$status = 1;
		$request = Input::get();
		// Load survey
		$survey = Survey::where('id', '=', Input::get('survey_id'))->first();
		$survey->publish = 3;
		$survey->save();

		foreach ($request['options_selected'] as $key_options_selected => $options_selected) {
			MasterCode::savingProcess($survey,$options_selected);
		}

		$insert_queue = DelayedJob::create(array('type' => 'importfile','survey_id' => $survey->id,'data' => count(Input::get('options_selected')),'queue' => 1));

		Session::flash('alert-class', 'alert-success'); 
		Session::flash('message', 'Importing File is in progress');
		
		return $status;
	}

	public function getManagesurvey($id)
	{
		// Load survey
		$request = array();
		$survey = Survey::where('id', '=', $id)->first();
		$request['survey_id'] = $survey->id;

		// Get Default Question
		$default_questions = Question::DefaultQuestion($request);

		$default_question = reset($default_questions);

		$request['category'] = $default_question->id_question_categories;

		// Get catefory and question list
		$question_categories_query = QuestionCategory::QuestionCategoryFilterRegion($request);
		$split_data = QuestionCategory::SplitQuestionsCategory($question_categories_query);
		$question_by_category = QuestionCategory::questionByCategory($request);

		$cycles = array();
		$loadcycles = Cycle::all();
		foreach ($loadcycles as $key_loadcycles => $loadcycle) {
			$cycles[$loadcycle->id] = $loadcycle->name;
		}

		$questions_all = array();
		foreach ($question_by_category as $key_question_lists => $value_question_list) {
			$questions_all[$value_question_list->id] = $value_question_list->question;
		}

		$data = array(
			"survey" => $survey,
			"filters" => Code::getFilter(),
			"cycles" => $cycles,
			"question_categories" => $split_data['question_categories'],
			"question_lists" => $questions_all,
			"default_question" => $default_question,
			"question" => $default_questions,
			"public_path" => public_path(),
			"regions" => QuestionParticipant::RegionColor($default_question->id_cycle,$default_questions),
		);
		return View::make('admin.survey.managesurvey', $data);
	}

	public function getDefaultquestion($id)
	{
		// Load survey
		$survey = Survey::where('id', '=', $id)->first();
		$survey->publish = 1;
		$survey->save();
		
		Session::flash('alert-class', 'alert-success'); 
		Session::flash('message', 'Save Succeed');

		return Redirect::to('/admin/survey');
	}

	public function postDefaultquestion()
	{
		// Save Default Question survey
		$question_previous = Question::where('is_default', '=', 1)->first();
		$question_previous->is_default = 0;
		$question_previous->save();

		$answer_default_previous = DB::table('answers')
			->where('cycle_default', 1)
			->where('question_id', $question_previous->id)
			->update(array(
         'cycle_default' => 0
      ));

		$question = Question::where('id', '=', Input::get('question_select'))->first();
		$question->is_default = 1;
		$question->save();

		$answers = DB::table('answers')
				->where('question_id', $question->id)
				->where('cycle_id', Input::get('cycle_select'))
				->update(array(
         'cycle_default' => 1
         ));

		Session::flash('alert-class', 'alert-success'); 
		Session::flash('message', 'Save Succeed');

		return Redirect::to('/admin/survey');
	}
	
	public function getCyclelist()
	{
		// Load Question
		$responses = Question::loadQuestionCycle(Input::get());
		$select = "";
		foreach ($responses as $key_responses => $response) {
			$select .= '<option value="'.$response->question_id.'">'.$response->question.'</option>';
		}
		return $select;
	}

	public function getCycle()
	{
		// Load Question
		$request = Input::get();
		$data = array(
			'cycle' => Cycle::where('id','=',$request['cycle_id'])->first(),
			'survey' => Survey::where('id', '=', $request['survey_id'])->first(),
			'questions' => Question::loadQuestionCycle(Input::get()),
		);

		return View::make('admin.survey.cycle', $data);
	}

	public function postCycle()
	{
		$post_cycle = Category::getDataList(Input::get());

		return $post_cycle;
	}

	public function deleteSurvey($id)
	{
		Survey::deleteSurvey($id);
		return Redirect::to('/admin/survey');
	}

	public static function fileRename($file_names)
	{
		$file_name_array = explode(".",$file_names);
		$file_name = $file_name_array[0];

		if(strtolower($file_name_array[1]) == "csv"){
			$file_name = explode(".",$file_name);
			$file_name = $file_name[0];
			$file_name = preg_replace('/[^A-Za-z0-9\-\s?\/#$%^&*()+=\-\[\];,.:<>|]\n\r/', '', $file_name);
			$file_name = str_replace('"', "", $file_name);
			$file_name = preg_replace('/\s+/', '', $file_name);
			$file_name = trim(preg_replace('/\s\s+/', ' ', $file_name));
			$file_name = strtolower($file_name);
		}else{
			$file_name = $file_names;
		}

		return $file_name;
	}
}