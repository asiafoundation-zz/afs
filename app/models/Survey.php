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
					$surveys[$key_survey_lists]['publish_text'] = "Importing";
					$surveys[$key_survey_lists]['publish_style'] = "importing";
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
		return $surveys;
	}

	public static function getSurveys()
	{
		$surveys = DB::table('surveys')->get();

		$surveys = self::surveyDetails($surveys);

		return $surveys;
	}

	public static function importData($survey,$master_code,$excel_data)
	{
		foreach ($excel_data as $lists_data) {
			$questions_list = array();
			$category_items = array();
			$i=0;
			foreach ($lists_data as $column => $data) {

				if (!empty($master_code[$column])) {
					// remove special characters and number
					$data_str = preg_replace('/[^A-Za-z]/', "", $data);

					switch ($master_code[$column]['type']) {
						case 0:
							// Check region exist
							$region_id = Region::checkData($data_str,$master_code[$column]['code_id']);
							break;
						case 1:
							$wave_id = strtolower($data) == 'baseline' ? 0 : 1;
							// Check wave exist
							$cycle_id = Cycle::checkData($data_str,$wave_id);
							$questions_list[$i]['cycle_id'] = $cycle_id;
							break;
						case 2:
							// Check oversample
							$oversample_id = preg_replace('/[^0-9]/', "", $data);
							$oversample_id = $oversample_id == 1 ? 0 : 1;
							break;
						case 3:
							// Check category exist
							$category = Category::checkData($data,$master_code[$column]['code_id'],$survey->id);
							$category_items[$i] = array(
								'data' => $data,
								'category_id' => $category->id
								);
							break;
						case 4:
							// Check answers exist
							$question_category = QuestionCategory::checkData($data,$master_code[$column]['code_id'],$survey->id);
							$questions_list[$i]['data'] = $data;
							$questions_list[$i]['code_id'] = $master_code[$column]['code_id'];
							$questions_list[$i]['question_category_id'] = $question_category->id;
							break;

						default:
							continue;
							break;
					}
				}
			}
			$i++;
			// Save participant
			$participant = new Participant;
			$participant->save();
			foreach ($category_items as $category_item) {
				$category_item_data = CategoryItem::checkData($category_item['data'],$category_item['category_id']);

				$filter_participant = new FilterParticipant;
				$filter_participant->category_item_id = $category_item_data->id;
				$filter_participant->participant_id = $participant->id;
				$filter_participant->save();
			}
			foreach ($questions_list as $question_list) {
				$question = Question::checkData('',$question_list['code_id'],$question_list['question_category_id']);
				$answer = Answer::checkData($question_list['data'],$question->id,$question_list['cycle_id']);

				$question_participant = new QuestionParticipant;
				$question_participant->answer_id = $answer->id;
				$question_participant->participant_id = $participant->id;
				$question_participant->region_id = $region_id;
				$question_participant->save();
			}
		}
		exit();
	}

	Public static function readHeader($inputFileName, $highest_column, $sheet)
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

	    // Set variable data
	    $data = array();
	    $data_header = array();

	    $objWorksheet = $objPHPExcel->getSheet($sheet);
	    $highestRow = $objWorksheet->getHighestRow();
	    $highestColumn = $highest_column;
	    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

	    if ($sheet == 0) {
				for($row = 5; $row <= $highestRow; ++$row){
					for($col = 0; $col <= $highestColumnIndex; ++$col){
						$dataval = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
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
							$data[$row][$data_header[$col]] = $dataval;
						}
					}
					// Break
					if (empty($data[$row][$first_column]) && $row != 1) {
						break;
					}
				}
	    }

	  return $data;
	}
}