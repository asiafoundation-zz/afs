<?php

class SurveyController extends AvelcaController {

	public function __construct(\Survey $Model)
	{
		parent::__construct($Model);
	}
	
	public function getIndex()
	{
		list($data['surveys'],$data['is_refresh'],$data['category_show']) = Survey::getSurveys();
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
		// Emptying mongo data
		$cursors = Assign::all();
		foreach ($cursors as $key => $cursor) {
			// Delete document in collections monggodb
			$assign_delete = Assign::find(['delayed_job_id'=>(string)$cursor->delayed_job_id])->first();
			// Delete actions
			$assign_delete->delete();
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
				$survey->baseline_file = $request['excel'];
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
			}
		}else{
			$rule = array('survey_name' => 'Required');

			$validator = Validator::make(Input::all(), $rule);

			if($validator->passes())
			{
				$survey = Survey::create(array('name' => Input::get('survey_name'), 'baseline_file' => Input::file('excel')->getClientOriginalName(), 'geojson_file' => Input::file('geojson')->getClientOriginalName(),'publish' => 3));

				if($survey)
				{
					self::postUpload($files);

					// Self to delayed Job
					$delayed_jobs = DelayedJob::create(array('type' => 'parsingfile','survey_id' => $survey->id,'data' => 0,'queue' => 1));
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
		
		$header = Header::find(['survey_id'=>$survey->id])->first();
		$header = json_decode($header->data);

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
		$survey->publish = 2;
		$survey->save();

		$insert_queue = DelayedJob::create(array('type' => 'importfile','survey_id' => $survey->id,'data' => count(Input::get('options_selected')),'queue' => 1));
		$delayed_job_id = $insert_queue->id;

		$assign = new Assign;
		$assign->survey_id = Input::get('survey_id');
		$assign->delayed_job_id = (string)$delayed_job_id;
		$assign->data = json_encode($request['options_selected']);
		$assign->save();
		
		Session::flash('alert-class', 'alert-success'); 
		Session::flash('message', 'Importing File is in progress');
		
		return $status;
	}

	public function getManagesurvey($id)
	{
		// Load survey
		$survey = Survey::where('id', '=', $id)->first();

		// Get Default Question
		$default_questions = Question::DefaultQuestion(Input::get());

		$default_question = reset($default_questions);

		// Get catefory and question list
		$question_categories_query = QuestionCategory::QuestionCategoryFilterRegion(Input::get());
		$split_data = QuestionCategory::SplitQuestionsCategory($question_categories_query);

		$cycles = array();
		$loadcycles = Cycle::QuestionCycle($default_question);
		foreach ($loadcycles as $key_loadcycles => $loadcycle) {
			$cycles[$loadcycle->id] = $loadcycle->name;
		}

		$questions_all = array();
		foreach ($split_data['question_lists'] as $key_question_lists => $value_question_list) {
			$questions_all[$value_question_list->id] = $value_question_list->question;
		}

		$data = array(
			"survey" => $survey,
			"filters" => Code::getFilter(),
			"cycles" => $cycles,
			// "cycles" => Cycle::get(),
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
}