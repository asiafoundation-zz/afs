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
			$survey = Survey::create(array('name' => Input::get('survey_name')));

			if($survey)
			{
				return Redirect::to('/admin/survey/cycle');
			}
		}
		else
		{
			return Redirect::to('/admin/survey')->withErrors($validator)->withInput();
		}
	}

	public function getCycle()
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
		$cycle = Cycle::where('id', '=', $id)->first();
		
		$header = $this->readHeader($cycle->excel_file, 'E', 0);

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
				
				$question = Question::where('code', '=', $question_content[1])->first();
				if(!isset($question))
				{
					Question::create(array('code' => $question_content[1], 'question' => $question_content[2], 'question_category_id' => $id_question_category));
				}
			}
		}

		$content = array("Select Region", "Please select region with clicking a list on the left");

		$form_action = "/admin/survey/region";

		$button_value = "Next";

		return View::make('admin.survey.import')
				->with('header', $headers)
				->with('content', $content)
				->with('action', $form_action)
				->with('button', $button_value)
				->with('id_cycle', Input::get('id_cycle'))
				->with('base_header', false);
	}

	public function postRegion(){
		$categories = Input::get('unselected');
		$region = Input::get('header');

		foreach($region as $value)
		{
			$region_piece = explode(';',$value);
			$code = Code::where('code','=',$region_piece[0])->first();
			if(!isset($code))
			{
				$code = Code::create(array('code' => $region_piece[0]));
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

		$cycle = Cycle::where('id', '=', Input::get('id_cycle'))->first();
		$filename = 'uploads/'.$cycle->excel_file;
		Excel::selectSheetsByIndex(1)->filter('chunk')->load($filename)->chunk(250, function($results)
		{
			$region = Input::get('header');

			$region_piece = explode(';', $region[0]);

			$data = array();

			$arr_data = $results->toArray();

			foreach($arr_data as $data)
			{
				foreach($data as $key => $value)
				{
					if($key == strtolower($region_piece[0]))
					{
						$s_region = Region::where('name', '=', $value)->first();
						if(!isset($s_region))
						{
							$code = Code::where('code', '=', $region_piece[0])->first();
							$region = Region::create(array('name' => $value, 'code_id' => $code->id));
							$region_id = $region->id;
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
				->with('id_cycle', Input::get('id_cycle'))
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

			$s_code = Code::where('code', '=', $arr_category[0])->first();
			if(!isset($s_code))
			{
				$code = Code::create(array('code' => $arr_category[0]));

				if($code)
				{
					$s_category = Category::where('name', '=', $arr_category[1])->first();
					if(!isset($s_category))
					{
						Category::create(array('name' => $arr_category[1], 'code_id' => $code->id));
					}
				}
			}
		}

		$cycle = Cycle::where('id', '=', Input::get('id_cycle'))->first();
		$filename = 'uploads/'.$cycle->excel_file;
		Excel::selectSheetsByIndex(1)->filter('chunk')->load($filename)->chunk(250, function($results)
		{
			$data = array();
			$oversample = Input::get('header');
			$arr_data = $results->toArray();
			$region_id = 0; 
			$sample_type = null;

			foreach($arr_data as $data)
			{
				foreach($data as $key => $value)
				{
					$code = Code::where('code', '=', $key)->first();
					if(isset($code))
					{

						$s_region = Region::join('codes', 'regions.code_id', '=', 'codes.id')->where('code', '=', strtoupper($key))->first();
						if(isset($s_region))
						{
							$region_id = $s_region->id;
						}
						else
						{
							$s_category_item = CategoryItem::where('name', '=', $value)->first();
							if(!isset($s_category_item))
							{
								if($value != "")
								{
									$category_item = CategoryItem::create(array('name' => $value, 'category_id' => $code->id));
									$category_id = $category_item->id;	
								}
							}	
						}
					}

					$arr_oversample = explode(';',$oversample[0]);
					if(strtolower($arr_oversample[0]) == $key){
						$sample_type = 1;
						$arr_value = explode('. ', $value);
						if(strtolower($arr_value[1]) == 'sample utama'){
							$sample_type = 0;
							
						}
					}

					$question = Question::where('code', '=', $key)->first();
					if(isset($question))
					{
						if($value != "")
						{
							$answer = Answer::create(array('answer' => $value, 'question_id' => $question->id, 'cycle_id' => Input::get('id_cycle')));

							$question_participant = QuestionParticipant::create(array('answer_id' => $answer->id, 'region_id' => $region_id, 'sample_type' => $sample_type));
						}
					}
				}
			}

		});

	}

	public function Import()
	{
		$headers = Input::get('header');
		$questions = Input::get('question');

		$string = array();

		/*INSERT FILTER*/

		foreach($headers as $header)
		{
			$string = explode(';', $header);
			$select_code = Code::where('code', '=', $string[0])->first();
			if(!isset($select_code))
			{
				$code = Code::create(array('code' => $string[0]));
				if($code)
				{
					$select_categories = Category::where('name', '=', $string[1])->first();
					if(!isset($select_categories))
					{
						$categories = Category::create(array('name' => $string[1], 'code_id' => $code->id));	
					}
				}	
			}
		}

		/*INSERT QUESTION*/

		foreach($questions as $question)
		{
			$string = explode(';', $question);
			$code = Code::where('code', '=', $string[0])->first();
			if(!isset($code))
			{
				$question = Question::where('code', '=', $string[0])->first();
				if(!isset($question)){
					Question::create(array('code' => $string[0], 'question' => $string[1]));
				}
			}
		}

		/*IMPORT DATA*/

		$cycle = Cycle::where('id', '=', Input::get('id_cycle'))->first();
		$filename = 'uploads/'.$cycle->excel_file;
		Excel::selectSheetsByIndex(1)->filter('chunk')->load($filename)->chunk(250, function($results)
		{
			$data = array();

			$arr_data = $results->toArray();

			foreach($arr_data as $data)
			{
				foreach($data as $key => $value)
				{
					$code = Code::where('code', '=', $key)->first();
					if(isset($code))
					{
						
						if(strpos($value, '. ') !== false){
							$string = array();
							$string = explode('. ', $value);
							$value = $string[1];
						}

						$s_category_item = CategoryItem::where('name', '=', $value)->first();
						if(!isset($s_category_item))
						{
							if($value != ""){
								$category_item = CategoryItem::create(array('name' => $value, 'category_id' => $code->id));
								$category_id = $category_item->id;	
							}
						}
						
						if($code->code == 'SFL_PROV')
						{
							$s_region = Region::where('name', '=', $value)->first();
							if(!isset($s_region))
							{
								$region = Region::create(array('name' => $value, 'code_id' => $code->id));
								$region_id = $region->id;
							}
						}

					}

					$question = Question::where('code', '=', $key)->first();
					if(isset($question)){
						// echo $value.'<br>';
						if($value != ""){
							$answer = Answer::create(array('answer' => $value, 'question_id' => $question->id, 'cycle_id' => Input::get('id_cycle')));	
						}
					}
				}
			}

		});
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