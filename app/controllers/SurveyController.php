<?php

class SurveyController extends AvelcaController {

	public function __construct(\Survey $Model)
	{
		parent::__construct($Model);
	}
	
	public function getIndex()
	{

		return View::make('admin.survey.index');
	}

	public function postIndex()
	{
		$rule = array('survey_name' => 'Required');

		$validator = Validator::make(Input::all(), $rule);

		if($validator->passes())
		{
			$survey = Survey::create(array('name' => Input::get('survey_name'), 'baseline_file' => Input::get('excel'), 'publish' => 1));

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

	/*public function getCycle()
	{

		return view::make('admin.survey.cycle');
	}

	public function postCycle()
	{
		$rule = array(
				'cycle_name' => 'Required',
				'excel' => 'Required'
			);

		$validator = Validator::make(Input::all(), $rule);

		if($validator->passes())
		{
			
			$cycle = Cycle::create(array('name' => Input::get('cycle_name'), 'excel_file' => Input::get('uploaded_file')));

			if($cycle)
			{
				return Redirect::to('/admin/survey/import/'. $cycle->id);
			}

		}
		else
		{
			return Redirect::to('/admin/survey/cycle')->withErrors($validator)->withInput();
		}
	}
*/
	public function postUpload(){
		
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
		
		$header = $this->readHeader($survey->baseline_file, 'E', 0);

		$content = array("Select Category Filter","Please select select category with clicking a list on the left");

		$form_action = "/admin/survey/category";

		$button_value = "Next";

		return View::make('admin.survey.import')
				->with('header', $header)
				->with('content', $content)
				->with('action', $form_action)
				->with('button', $button_value)
				->with('base_header', true);
	}

	public function postCategory(){
		$headers = Input::get('header');
		$questions = Input::get('unselected');

		$header_code = array();
		foreach($headers as $header)
		{
			$header_content = explode(';', $header);
			$header_code[] = $header_content[0];
		}

		$id_question_category = 0;
		foreach($questions as $question)
		{
			$question_content = explode(';', $question);
			$question_exist = in_array($question_content[1], $header_code);
			
			if(empty($question_exist))
			{
				if(!empty($question_content[0]))
				{
					$select_question_category = QuestionCategory::where('name', '=', $question_content[0])->first();
					if(!isset($select_question_category))
					{
						$question_category = QuestionCategory::create(array('name' => $question_content[0], 'survey_id' => 1));
						$id_question_category = $question_category->id;
					}
					else{ $id_question_category = $select_question_category->id; }
				}

				$arr_master_code = explode('_', $question_content[1]);
				$s_master_code = MasterCode::where('master_code', '=', $arr_master_code[0])->first();
				if(!isset($s_master_code))
				{	
					$master_code = MasterCode::create(array('master_code' => $arr_master_code[0]));

					if($master_code)
					{
						if(empty($arr_master_code[1])){ 
							$code_content = "-"; 
						}else{
							$code_content = $arr_master_code[1];	
						}
						

						$code = Code::create(array('code' => $code_content, 'master_code_id' => $master_code->id));
						
						Question::create(array('code_id' => $code->id, 'question' => $question_content[2], 'question_category_id' => $id_question_category));	
					}
				}
			}

		}

		$question = Question::find(1);
		$question->is_default = 1;
		$question->save();

		$content = array("Select Region", "Please select region with clicking a list on the left");

		$form_action = "/admin/survey/region";

		$button_value = "Next";

		return View::make('admin.survey.import')
				->with('header', $headers)
				->with('content', $content)
				->with('action', $form_action)
				->with('button', $button_value)
				->with('survey_id', Input::get('survey_id'))
				->with('base_header', false);
	}

	public function postRegion(){
		$categories = Input::get('unselected');
		$region = Input::get('header');
		$code_id = 0;
		$master_code_id = 0;

		foreach($region as $value)
		{
			$region_piece = explode(';',$value);

			$code_piece = explode('_', $region_piece[0]);

			$s_master_code = MasterCode::where('master_code','=', $code_piece[0])->first();
			if(!isset($s_master_code))
			{
				$master_code = MasterCode::create(array('master_code' => $code_piece[0]));
				$master_code_id = $master_code->id;
			}

			$code = Code::where('code','=',$code_piece[1])->first();
			if(!isset($code))
			{
				$code = Code::create(array('code' => $code_piece[1], 'master_code_id' => $master_code_id));
				$code_id = $code->id;
			}

			$category = Category::where('name', '=', $region_piece[1])->first();
			if(!isset($category))
			{
				Category::create(array('name' => $region_piece[1], 'code_id' => $code_id));
			}
		}

		$region_exist = array_search($region[0], $categories);
		if($region_exist)
		{
			unset($categories[$region_exist]);
			$categories = array_values($categories);
		}

		$survey = Survey::where('id', '=', Input::get('survey_id'))->first();
		$filename = 'uploads/'.$survey->baseline_file;
		Excel::selectSheetsByIndex(1)->filter('chunk')->load($filename)->chunk(250, function($results)
		{
			$region = Input::get('header');
			$region_piece = explode(';', $region[0]);
			$code_piece = explode('_', $region_piece[0]);
			$data = array();

			$arr_data = $results->toArray();

			foreach($arr_data as $data)
			{
				foreach($data as $key => $value)
				{
					$key_piece = explode('_', $key);
					
					if(!empty($key_piece[1]))
					{
						
						if($key_piece[1] == strtolower($code_piece[1]))
						{
							$region_name = explode('. ', $value);

							$s_region = Region::where('name', '=', $region_name[1])->first();
							if(!isset($s_region))
							{
								$code = Code::where('code', '=', strtolower($key_piece[1]))->first();

								$region = Region::create(array('name' => $region_name[1], 'code_id' => $code->id));
							}
						}	
					}
				}
			}
		});	

		$content = array("Select Oversample", "Please select Oversample data with clicking a list on the left");

		$form_action = "/admin/survey/oversample";

		$button_value = "Next";

		return View::make('admin.survey.import')
				->with('header', $categories)
				->with('content', $content)
				->with('action', $form_action)
				->with('button', $button_value)
				->with('survey_id', Input::get('survey_id'))
				->with('base_header', false);

	}

	public function postOversample(){
		$categories = Input::get('unselected');
		$oversample = Input::get('header');

		$oversample_exist = array_search($oversample[0], $categories);
		if($oversample_exist)
		{
			unset($categories[$oversample_exist]);
			$categories = array_values($categories);
		}

		foreach($categories as $category){
			$arr_category = explode(';',$category);
			$arr_code = explode('_', $arr_category[0]);

			$s_master_code = MasterCode::where('master_code', '=', $arr_code[0])->first();
			if(isset($s_master_code)){
				$s_code = Code::where('code', '=', $arr_code[1])->first();
				if(!isset($s_code))
				{
					$code = Code::create(array('code' => $arr_code[1], 'master_code_id' => $s_master_code->id));

					$s_category = Category::where('name', '=', $arr_category[1])->first();
					if(!isset($s_category))
					{
						Category::create(array('name' => $arr_category[1], 'code_id' => $code->id));
					}	
				}	
			}
		}

		$survey = Survey::where('id', '=', Input::get('survey_id'))->first();
		$filename = 'uploads/'.$survey->baseline_file;
		Excel::selectSheetsByIndex(1)->filter('chunk')->load($filename)->chunk(250, function($results)
		{
			
			$oversample = Input::get('header');
			$categories = Input::get('unselected');

			$data = array();
			$region_id = 0; 
			$category_item_id = 0;
			$cycle_id = 0;
			$sample_type = null;
			$participant_count = 1;

			// $code = array();
			// $filter = array();
			foreach($categories as $category)
			{
				$arr_category = explode(';', $category);
				$code_piece = explode('_', $arr_category[0]);
				$master_code = $code_piece[0];
				$filter[] = $arr_category[1];
			}

			$arr_data = $results->toArray();

			$index = 0;
			foreach($arr_data as $data)
			{	
				$participant = Participant::create(array('id' => $participant_count));
				$participant_id = $participant->id;

				foreach($data as $key => $value)
				{	
					$key_piece = explode('_', $key);

					if(strtolower($master_code) == $key_piece[0])
					{
						if(!empty($key_piece[1]))
						{
							if($key_piece[1] == strtolower($filter[0]))
							{
								$cycle_type = 0;

								if(strtolower($value) == 'endline')
								{
									$cycle_type = 1;
								}

								$s_cycle = Cycle::where('name','=', strtolower($value))->first();
								if(!isset($s_cycle))
								{
									$cycle = Cycle::create(array('name' => strtolower($value), 'cycle_type' => $cycle_type));
									$cycle_id = $cycle->id;
								}	
							}

							$code = Code::where('code', '=', $key_piece[1])->first();
							if(isset($code))
							{
								$s_region = DB::table('regions')
											->select(DB::raw('regions.id as region_id'))
											->join('codes', 'regions.code_id', '=', 'codes.id')
											->where('codes.code', '=', strtoupper($key_piece[1]))
											->first();

								if(isset($s_region))
								{
									$region_id = $s_region->region_id;
								}
								else
								{
									$s_category_item = CategoryItem::where('name', '=', $value)->first();
									if(!isset($s_category_item))
									{
										if($value != "")
										{
											$category_id = Category::where('code_id', '=', $code->id)->first()->id;

											$category_item = CategoryItem::create(array('name' => $value, 'category_id' => $category_id));
											$category_item_id = $category_item->id;
										}
									}
								}

								$s_category_item = CategoryItem::where('name', '=', $value)->first();
								if(isset($s_category_item)){
									FilterParticipant::create(array('participant_id' => $participant_id, 'category_item_id' => $s_category_item->id));
								}
							}
						}
					}
					
					$arr_oversample = explode(';',$oversample[0]);
					$oversample_code = explode('_', $arr_oversample[0]);

					if(!empty($key_piece[1]))
					{
						if(strtolower($oversample_code[1]) == $key_piece[1]){
							$sample_type = 1;
							$arr_value = explode('. ', $value);
							if(strtolower($arr_value[1]) == 'sample utama'){
								$sample_type = 0;
							}
						}	
					}
					

					$question = DB::table('questions')
								->select(
										DB::raw('questions.id as question_id')
									)
								->join('codes', 'codes.id', '=', 'questions.code_id')
								->join('master_codes', 'master_codes.id', '=', 'codes.master_code_id') 
								->where('master_code', '=', $key_piece[0])->first();

					if(isset($question))
					{
						if($value != "")
						{
							$answer = Answer::create(array('answer' => $value, 'question_id' => $question->question_id, 'cycle_id' => $cycle_id, 'color_id' => 1));

							$question_participant = QuestionParticipant::create(array('answer_id' => $answer->id, 'region_id' => $region_id, 'sample_type' => $sample_type, 'participant_id' => $participant_id));
						}
					}

				}
				$index ++;
				$participant_count ++;
			}

		});
		
		return Redirect::to('/admin/survey/managesurvey');
	}

	public function getManagesurvey(){
		$question = Question::all();
		return View::make('admin.survey.managesurvey')->with('question', $question);
	}

	public function readHeader($inputFileName, $highest_column, $sheet)
	{
		$inputFileName = '../public/uploads/'.$inputFileName;

		try
	    {
	      $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
	      $objReader = PHPExcel_IOFactory::createReader($inputFileType);
	      $objPHPExcel = $objReader->load($inputFileName);
	    }
	    catch(Exception $e)
	    {
	        die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
	    }

	    if($highest_column == strtoupper('highes column')){
	    	$highest_column = $objWorksheet->getHighestColumn();
	    }

	    $objWorksheet = $objPHPExcel->getSheet($sheet);
	    $highestRow = $objWorksheet->getHighestRow();
	    $highestColumn = $highest_column;
	    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

	    for($row = 5; $row <= $highestRow; ++$row){
		
			for($col = 0; $col <= $highestColumnIndex; ++$col){

				$dataval = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				$data[$row]['header'.$col] = $dataval;
			}
		}

	    return $data;
	}

	public function read_string($string, $key_start, $key_end, $str_start, $str_end){
		$posfirst = strpos($string, $key_start);
		$poslast = strpos($string, $key_end);
		$start_point = $posfirst + $str_start;
		$end_point = $poslast + $str_end;
		$value = strtoupper(substr($string, $start_point, $end_point));

		return $value;
	}	
}