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

	public function getImport($id)
	{
		$cycle = Cycle::where('id', '=', $id)->first();
		
		$header = $this->readHeader($cycle->excel_file, 'E', 0);

		return View::make('admin.survey.import')->with('header', $header);
	}

	public function postImport()
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