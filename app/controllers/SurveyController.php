<?php

class SurveyController extends AvelcaController {

	public function __construct(\Survey $Model)
	{
		parent::__construct($Model);
	}
	
	public function getIndex(){

		return View::make('admin.survey.index');
	}

	public function getCycle(){

		return view::make('admin.survey.cycle');
	}

	public function getUpload(){

		return View::make('admin.survey.upload');
	}
	
	public function postUpload(){
		ini_set('memory_limit','256M');
		ini_set('max_execution_time', 300);

		$file = Input::file('excel');

		$filename = $file->getClientOriginalName();

		if(!file_exists($filename)){
			$uploaded = $file->move('uploads/', $filename);	
		}
		
	    $inputFileName = '../public/uploads/'.$filename;
	    try {
	      $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
	      $objReader = PHPExcel_IOFactory::createReader($inputFileType);
	      $objPHPExcel = $objReader->load($inputFileName);
	    }catch(Exception $e) {
	        die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
	    }

	    $objWorksheet = $objPHPExcel->getSheet(0);
	    $highestRow = $objWorksheet->getHighestRow();
	    $highestColumn = $objWorksheet->getHighestColumn();
	    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

	    for ($row = 3; $row <= 3; ++$row) {
	    	for($col = 0; $col <= $highestColumnIndex; ++$col){
	    		$dataval = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();

    			if($col == 1){
    				$province_string = 'JAWABAN';
    			}

    			$province_string = $this->read_string($dataval, '.', 'NO_PARAM', +2, strlen($dataval));
		        $arr_province[] = $province_string;
		        $province = Region::where('name', '=', $province_string)->first();

		        if($province_string != ''){
		        	if(!isset($province)){
		        		Region::create(array('name' => $province_string));
		        	}else{
			        	if($province->name != $province_string){
			        		Region::create(array('name' => $province_string));
			        	}
			        }
		        }
	      	}
	    }

	    $age1_pos = array_search('16-20 tahun', $arr_province);
	    // exit;
	    $select_region = Region::all();
	    $question_id = 0;
	    $answer_id = 0;
	    for($row = 0; $row <= $highestRow; ++$row){
			$rowdata = $row;
	      
	    	$counter = 0;
			for($col = 0; $col <= $highestColumnIndex; ++$col){

				$dataval = $objWorksheet->getCellByColumnAndRow($col, $rowdata)->getValue();
				if($question_id != 0){
					$answer_data = $objWorksheet->getCellByColumnAndRow(1, $rowdata)->getValue();
					if($answer_data != ""){
						$answer = Answer::create(array('question_id' => $question_id, 'answer' => $answer_data));
						$answer_id = $answer->id;
					}
					
					foreach($select_region as $region){
						if($counter == $select_region->count()) break;

						$amount_data = 	$objWorksheet->getCellByColumnAndRow(array_search($region->name, $arr_province), $rowdata)->getValue();
						if($amount_data != ''){
							Questioner::create(array('answer_id' => $answer_id, 'amount' => $amount_data, 'region_id' => $region->id));
						}
						$counter++;	
					}
				}

				$data[$row][$col] = $dataval;

				if(strpos($dataval,'Kode') !== false){
					$question_string = $this->read_string($dataval, '.', 'NO_PARAM', +2, strlen($dataval));
					$unique_code = $this->read_string($dataval, '[', ']', +1, -6);
					$code = $this->read_string($dataval, '[', 'NO_PARAM', +1, 3);
					$code_number = $this->read_string($dataval, '[', ']', +4, -9);

					$question_select = Question::where('code', '=', $unique_code)->first();
					if(!isset($question_select)){
						$question = Question::create(array('question' => $question_string, 'code' => $unique_code));
					}else{
						if($question_select->unique_code != $unique_code){
							$question = Question::create(array('question' => $question_string, 'code' => $unique_code));	
						}
					}

					$question_id = isset($question->id) ? $question->id : $question_select->id;
					// echo $question. ' | '. $unique_code .' | '. $code .' | '. $code_number;
				  	$row = $row + 2;
				  	break;
				}

				break;			
			}

			$rowdata++;
	    }
	    echo "<pre>";
	    print_r($arr_province);
	    echo "</pre>";

	    echo "<pre>";
	    print_r($data);
	    echo "</pre>";

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