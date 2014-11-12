<?php
class Survey extends Eloquent {

/*
 *	Publish Code:
 * 0 = not active
 * 1 = publish
 * 2 = uploading/importing
 * 3 = Complete
 * 4 = Unpublish
 */

	/* Soft Delete */
	protected $softDelete = true;

	/* Eloquent */
	public $table = "surveys";
	public $timestamps = true;


	/* Disabled Basic Actions */
	public static $disabledActions = array();

	/* Route */
	public $route = 'survey';

	/* Mass Assignment */
	protected $fillable = array(
		'name',
		'geojson_file',
		'baseline_file',
		'endline_file',
		'publish'
		);
	protected $guarded = array('id');

	/* Rules */
	public static $rules = array(
		'name' => 'required',
		'geojson_file' => 'required',
		'baseline_file' => 'required',
		'endline_file' => 'required',
		'publish' => 'required'
		);

	/* Database Structure */
	public static function structure()
	{
		$fields = array(
			'name' => array(
			'type' => 'text',
			'onIndex' => true
			),
			'geojson_file' => array(
						'type' => 'text',
						'onIndex' => true
					),
			'baseline_file' => array(
						'type' => 'text',
						'onIndex' => true
					),
			'endline_file' => array(
						'type' => 'text',
						'onIndex' => true
					),
			'publish' => array(
						'type' => 'number',
						'onIndex' => true
					)
		);

		return compact('fields');
	}

	public static function surveyDetails($survey_lists)
	{
		$surveys = array();
		$is_refresh = false;
		foreach ($survey_lists as $key_survey_lists => $survey_list) {
			$surveys[$key_survey_lists]['id'] = $survey_list->id;
			$surveys[$key_survey_lists]['name'] = $survey_list->name;
			$surveys[$key_survey_lists]['publish'] = $survey_list->publish;

			switch ($survey_list->publish) {
				case 0:
					$surveys[$key_survey_lists]['publish_text'] = "Not Active";
					$surveys[$key_survey_lists]['publish_style'] = "not_active";
					break;
				case 1:
					$surveys[$key_survey_lists]['publish_text'] = "Published";
					$surveys[$key_survey_lists]['publish_style'] = "published";
					break;
				case 2:
					$percentage = 0;
					// Is queue exist
					$queue = DelayedJob::where('survey_id','=',$survey_list->id)->first();
					if ((int)$queue->queue < (int)$queue->information) {
						$participant_count = Participant::where('survey_id','=',$survey_list->id)->count();
						$percentage = ((int)$participant_count / (int)$queue->information) * 100;
						$percentage = round($percentage);
					}
					elseif ((int)$queue->queue >= (int)$queue->information){
						$percentage = 99;
					}

					$surveys[$key_survey_lists]['publish_text'] = "Importing ";
					$surveys[$key_survey_lists]['publish_style'] = "importing";
					$surveys[$key_survey_lists]['percentage'] = $percentage;
					$is_refresh = true;
					break;
				case 3:
					$surveys[$key_survey_lists]['publish_text'] = "Completed";
					$surveys[$key_survey_lists]['publish_style'] = "completed";
					break;
				case 3:
					$surveys[$key_survey_lists]['publish_text'] = "Unpublish";
					$surveys[$key_survey_lists]['publish_style'] = "unpublish";
					break;

				default:
					$surveys[$key_survey_lists]['publish_text'] = "Not Active";
					$surveys[$key_survey_lists]['publish_style'] = "not_active";
					break;
			}
		}
		return array($surveys,$is_refresh);
	}

	public static function getSurveys()
	{
		$surveys = DB::table('surveys')->get();

		$surveys = self::surveyDetails($surveys);

		return $surveys;
	}

	public static function importData($survey,$master_code,$excel_data)
	{
		set_time_limit(0);
		$status = 0;
		// try{
		// 	DB::beginTransaction();
			foreach ($excel_data as $lists_data) {
				$questions_list = array();
				$category_items = array();
				$i=0;$j=0;
				foreach ($lists_data as $column => $data) {

					if (!empty($master_code[$column])) {

						// remove special characters and number
						$data_str = preg_replace('/[^A-Za-z\s]/', "", $data);

						switch ($master_code[$column]['type']) {
							case 0:
								// Check region exist
								$data_str = $data_str[0] == " " ? substr($data_str,1) : $data_str;
								$region_id = Region::checkData($data_str,$master_code[$column]['code_id']);
								break;
							case 1:
								$cycle_type = strtolower($data) == 'baseline' ? 0 : 1;
								// Check wave exist
								$cycle_id = Cycle::checkData($data_str,$cycle_type);
								break;
							case 2:
								// Check oversample
								$oversample_id = preg_replace('/[^0-9]/', "", $data);
								$oversample_id = $oversample_id == 1 ? 0 : 1;
								break;
							case 3:
								$column_piece = explode("_", $column);
								$code_label = !empty($column_piece[1]) ? $column_piece[1] : "";

								// Check category exist
								$category = Category::checkData($code_label,$master_code[$column]['code_id'],$survey->id);
								$category_items[$i] = array(
									'data' => $data,
									'category_id' => $category->id
									);
								$i++;
								break;
							case 4:
								// Check answers exist
								$question_category = QuestionCategory::checkData($data,$master_code[$column]['code_id'],$survey->id);

								$questions_list[$j]['cycle_id'] = $cycle_id;
								$questions_list[$j]['data'] = $data;
								$questions_list[$j]['code_id'] = $master_code[$column]['code_id'];
								$questions_list[$j]['question_category_id'] = $question_category->id;
								$j++;
								break;

							default:
								continue;
								break;
						}
					}
				}

				// Save participant
				$participant = new Participant;
				$participant->survey_id = $survey->id;
				$participant->save();
				foreach ($category_items as $category_item) {
					if (!empty($category_item['data'])) {
						$category_item_data = CategoryItem::checkData($category_item['data'],$category_item['category_id']);

						$filter_participant = new FilterParticipant;
						$filter_participant->category_item_id = $category_item_data->id;
						$filter_participant->participant_id = $participant->id;
						$filter_participant->save();
					}
				}

				foreach ($questions_list as $key => $question_list) {
					if (!empty($question_list['data'])) {
						$question = Question::checkData('',$question_list['code_id'],$question_list['question_category_id']);
						$answer = Answer::checkData($question_list['data'],$question->id,$question_list['cycle_id'], $key);

						$question_participant = QuestionParticipant::checkData($answer->id,$participant->id,$region_id);
					}
				}
			}
			// Set default question
			$default_question = Question::join('question_categories', 'question_categories.id','=','questions.question_category_id')->where('question_categories.survey_id','=',$survey->id)->orderBy('questions.id', 'DESC')->first();
			$default_question->is_default = 1;
			$default_question->save();

			$answer_default = DB::table('answers')
				->where('question_id', $default_question->id)
				->where('cycle_id', $question_list['cycle_id'])
				->update(array(
         'cycle_default' => 1
         ));

		// 	DB::commit();
		// 	$status = 1;
		// }
		// catch(\PDOException $e){
  //     DB::rollback();
  //     $status = 0;
  //   }
    return $status;
	}

	Public static function readHeader($inputFileName, $highest_column, $sheet)
	{
		$inputFileName = public_path().'/uploads/'.$inputFileName;

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

	    // if($highest_column == strtoupper('highes column')){
	    // 	$highest_column = $objWorksheet->getHighestColumn();
	    // }

	    // Set variable data
	    $data = array();
	    $data_header = array();

	    $objWorksheet = $objPHPExcel->getSheet($sheet);

	    $highestRow = $objWorksheet->getHighestRow();
	    $highestColumn = $highest_column;
	    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

	    // Reformat Array
	    if ($sheet == 0) {
				for($row = 5; $row <= $highestRow; ++$row){
					for($col = 0; $col <= $highestColumnIndex; ++$col){
						$dataval = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
						if ($col != 1) {
							$dataval = preg_replace('/[^A-Za-z0-9\-\s?\/#$%^&*()+=\-\[\];,.:<>|]/', '', $dataval);
						}

						$data[$row]['header'.$col] = $dataval;
					}

					if (empty($data[$row]['header1'])) {
						break;
					}
				}
	    }else{
	    	for($row = 1; $row <= $highestRow; ++$row){
					for($col = 0; $col <= $highestColumnIndex; ++$col){
						$dataval = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
						if ($row == 1) {
							$first_column = strtolower($dataval);
							$data_header[$col] = strtolower($dataval);
						}
						else
						{
							$dataval = preg_replace('/[^A-Za-z0-9\-\s?\/#$%^&*()+=\-\[\];,.:<>|]/', '', $dataval);
							$data[$row][$data_header[$col]] = $dataval;
						}
					}
					// Break
					if (empty($data[$row]) && $row != 1) {
						break;
					}
				}
	    }
	  return $data;
	}
}