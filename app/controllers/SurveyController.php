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
				return Redirect::to('/admin/survey/baseline');
			}
		}
		else
		{
			return Redirect::to('/admin/survey')->withErrors($validator)->withInput();
		}
	}

	public function getBaseline()
	{

		return view::make('admin.survey.baseline');
	}

	public function postBaseline()
	{
		$rule = array(
				'cycle_name' => 'Required',
				'excel' => 'Required'
			);

		$validator = Validator::make(Input::all(), $rule);

		if($validator->passes())
		{
			$cycle = Cycles::create(array('name' => Input::get('cycle_name')));

			if($cycle)
			{
				$this->import_excel($cycle->id, Input::file('excel'));
				return Redirect::to('/admin/survey/endline');
			}
		}
		else
		{
			return Redirect::to('/admin/survey/baseline')->withErrors($validator)->withInput();
		}
	}

	public function postUpload(){
		return Input::file('file')->getClientOriginalName();
	}

	public function getEndline()
	{

		return View::make('admin.survey.endline');
	}
	
	public function import_excel($cycle_id, $file)
	{
		$filename = $file->getClientOriginalName();

		if(!file_exists($filename))
		{
			$uploaded = $file->move('uploads/', $filename);	
		}
		
	    $inputFileName = '../public/uploads/'.$filename;
	    
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

	    $objWorksheet = $objPHPExcel->getSheet(0);
	    $highestRow = $objWorksheet->getHighestRow();
	    $highestColumn = $objWorksheet->getHighestColumn();
	    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

	    for ($row = 3; $row <= 3; ++$row)
	    {
	    	for($col = 0; $col <= $highestColumnIndex; ++$col)
	    	{
	    		$dataval = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();

    			if($col == 1)
    			{
    				$province_string = 'ANSWER';
    			}

    			$province_string = $this->read_string($dataval, '.', 'NO_PARAM', +2, strlen($dataval));
		        $arr_province[] = $province_string;
		        $province = Region::where('name', '=', $province_string)->first();

		        if($province_string != '')
		        {
		        	if(!isset($province))
		        	{
		        		Region::create(array('name' => $province_string));
		        	}
		        	else
		        	{
			        	if($province->name != $province_string)
			        	{
			        		Region::create(array('name' => $province_string));
			        	}
			        }
		        }
	      	}
	    }

	    $select_region = Region::all();
	    $question_id = 0;
	    $answer_id = 0;
	    for($row = 0; $row <= $highestRow; ++$row)
	    {
			$rowdata = $row;
	      
	    	$counter = 0;
			for($col = 0; $col <= $highestColumnIndex; ++$col)
			{

				$dataval = $objWorksheet->getCellByColumnAndRow($col, $rowdata)->getValue();
				if($question_id != 0){
					$answer_data = $objWorksheet->getCellByColumnAndRow(1, $rowdata)->getValue();
					if($answer_data != "")
					{
						$answer = Answer::create(array('question_id' => $question_id, 'answer' => $answer_data));
						$answer_id = $answer->id;
					}
					
					foreach($select_region as $region){
						if($counter == $select_region->count()) break;

						$amount_data = 	$objWorksheet->getCellByColumnAndRow(array_search($region->name, $arr_province), $rowdata)->getValue();
						if($amount_data != '')
						{
							Questioner::create(array('answer_id' => $answer_id, 'amount' => $amount_data, 'region_id' => $region->id));
						}
						$counter++;	
					}
				}

				if(strpos($dataval,'Code') !== false)
				{
					$question_string = $this->read_string($dataval, '.', 'NO_PARAM', +2, strlen($dataval));
					$unique_code = $this->read_string($dataval, '[', ']', +1, -6);
					$code = $this->read_string($dataval, '[', 'NO_PARAM', +1, 3);
					$code_number = $this->read_string($dataval, '[', ']', +4, -9);

					$question_select = Question::where('code', '=', $unique_code)->first();
					if(!isset($question_select))
					{
						$question = Question::create(array('question' => $question_string, 'code' => $unique_code, 'cycle_id' => $cycle_id));
					}
					else
					{
						if($question_select->unique_code != $unique_code)
						{
							$question = Question::create(array('question' => $question_string, 'code' => $unique_code, 'cycle_id' => $cycle_id));
						}
					}

					$question_id = isset($question->id) ? $question->id : $question_select->id;
				  	$row = $row + 2;
				  	break;
				}

				break;			
			}

			$rowdata++;
	    }
	    unset($data);
	    unset($startrow);
	    unset($objPHPExcel);
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