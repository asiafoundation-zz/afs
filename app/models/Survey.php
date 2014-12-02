<?php
class Survey extends Eloquent {

/*
 *	Publish Code:
 * 0 = not active
 * 1 = publish
 * 2 = uploading/importing
 * 3 = parsing
 * 4 = Complete
 * 5 = Unpublish
 * 6 = Category
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
		'publish',
		'is_default'
		);
	protected $guarded = array('id');

	/* Rules */
	public static $rules = array(
		'name' => 'required',
		'geojson_file' => 'required',
		'baseline_file' => 'required',
		'endline_file' => 'required',
		'publish' => 'required',
		'is_default' => 'required'
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
					),
			'is_default' => array(
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
		$category_show = false;
		$category_id = 0;
		foreach ($survey_lists as $key_survey_lists => $survey_list) {
			$surveys[$key_survey_lists]['id'] = $survey_list->id;
			$surveys[$key_survey_lists]['name'] = $survey_list->name;
			$surveys[$key_survey_lists]['publish'] = $survey_list->publish;
			$surveys[$key_survey_lists]['is_default'] = $survey_list->is_default;

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
					elseif(!isset($queue) && Participant::count() > 0){
						if ((int)$queue->queue >= (int)$queue->information){
							$percentage = 99;
						}
					}

					$surveys[$key_survey_lists]['publish_text'] = "Importing ";
					$surveys[$key_survey_lists]['publish_style'] = "importing";
					$surveys[$key_survey_lists]['percentage'] = $percentage;
					$is_refresh = true;
					break;
				case 3:
					$surveys[$key_survey_lists]['publish_text'] = "Parsing File";
					$surveys[$key_survey_lists]['publish_style'] = "parsing";
					$is_refresh = true;
					break;
				case 4:
					$surveys[$key_survey_lists]['publish_text'] = "Completed";
					$surveys[$key_survey_lists]['publish_style'] = "completed";
					break;
				case 5:
					$surveys[$key_survey_lists]['publish_text'] = "Unpublish";
					$surveys[$key_survey_lists]['publish_style'] = "unpublish";
					break;
				case 6:
					$surveys[$key_survey_lists]['publish_text'] = "Select Category";
					$surveys[$key_survey_lists]['publish_style'] = "category";
					$category_show = true;
					$category_id = $survey_list->id;
					break;

				default:
					$surveys[$key_survey_lists]['publish_text'] = "Not Active";
					$surveys[$key_survey_lists]['publish_style'] = "not_active";
					break;
			}
		}
		return array($surveys,$is_refresh,$category_show,$category_id );
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
		// parse data
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
						$oversample_id = 0;
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
								$question_category = Question::select(
													DB::raw(
																'question_categories.id as question_category_id'
															)
														)
													->join('question_categories', 'question_categories.id', '=', 'questions.question_category_id')
													->where('questions.code_id', '=', $master_code[$column]['code_id'])
													->first();

								$questions_list[$j]['cycle_id'] = $cycle_id;
								$questions_list[$j]['sample_type'] = $oversample_id;
								$questions_list[$j]['data'] = $data;
								$questions_list[$j]['code_id'] = $master_code[$column]['code_id'];
								$questions_list[$j]['question_category_id'] = $question_category->question_category_id;
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
				foreach ($questions_list as $key => $question_list) {
					if (!empty($question_list['data'])) {
						$question = Question::where('code_id','=',$question_list['code_id'])->where('question_category_id','=',$question_list['question_category_id'])->first();

						$answer = Answer::checkData($question_list['data'],$question->id,$question_list['cycle_id'],$key);
						
						$question_participant = QuestionParticipant::checkData($answer->id,$participant->id,$region_id,$question_list['sample_type']);
					}
				}

				foreach ($category_items as $category_item) {
					if (!empty($category_item['data'])) {
						$category_item_data = CategoryItem::checkData($category_item['data'],$category_item['category_id']);
						$filter_participant = new FilterParticipant;
						$filter_participant->category_item_id = $category_item_data->id;
						$filter_participant->participant_id = $participant->id;
						$filter_participant->save();
					}
				}
				AmountFilter::checkData($participant->id);
			}echo "a"; die();
		// 	DB::commit();
		// 	$status = 1;
		// }
		// catch(\PDOException $e){
  //     DB::rollback();
  //     $status = 0;
  //   }
    return $status;
	}

	Public static function readHeader($survey,$delayed_jobs = array())
	{
		set_time_limit(0);
		$inputFileName = public_path().'/uploads/'.$survey->baseline_file;

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

	    // Set variable data
	    $data_label = array();
	    $data = array();
	    $data_header = array();

	    $objWorksheet_1 = $objPHPExcel->getSheet(0);
	    $objWorksheet_2 = $objPHPExcel->getSheet(1);

	    $highest_column_1 = $objWorksheet_1->getHighestColumn();
	    $highest_column_2 = $objWorksheet_2->getHighestColumn();

	    $highestRow_1 = $objWorksheet_1->getHighestRow();
	    $highestRow_2 = $objWorksheet_2->getHighestRow();

	    $highestColumnIndex_1 = PHPExcel_Cell::columnIndexFromString($highest_column_1);
	    $highestColumnIndex_2 = PHPExcel_Cell::columnIndexFromString($highest_column_2);

	    // Reformat Array
			for($row = 5; $row <= $highestRow_1; ++$row){
				for($col = 0; $col <= $highestColumnIndex_1; ++$col){
					$dataval = $objWorksheet_1->getCellByColumnAndRow($col, $row)->getValue();
					if ($col != 1) {
						$dataval = preg_replace('/[^A-Za-z0-9\-\s?\/#$%^&*()+=\-\[\];,.:<>|\n\r]/', '', $dataval);
						$dataval = trim(preg_replace('/\s\s+/', ' ', $dataval));
					}
					$data_label[$row]['header'.$col] = $dataval;
				}

				if (empty($data_label[$row]['header1'])) {
					break;
				}
			}
			// Save header to MongoDB
			if (!empty($data_label)) {
				$header = new Header;
				$header->survey_id = $survey->id;
				$header->delayed_job_id = $delayed_jobs->id;
				$header->data = json_encode($data_label);
				$header->save();
			}

    	for($row = 1; $row <= $highestRow_2; ++$row){
				for($col = 0; $col <= $highestColumnIndex_2; ++$col){
					$dataval = $objWorksheet_2->getCellByColumnAndRow($col, $row)->getValue();

					$dataval_header = $objWorksheet_2->getCellByColumnAndRow($col, $row)->getValue();
					$dataval_header = preg_replace('/[^A-Za-z0-9\-\s?\/#$%^&*()+=\-\[\];,.:<>|]\n\r/', '', $dataval_header);
					
					if ($row == 1) {
						$first_column = strtolower($dataval);
						$data_header[$col] = strtolower($dataval);
					}
					else
					{
						$dataval = preg_replace('/[^A-Za-z0-9\-\s?\/#$%^&*()+=\-\[\];,.:<>|]\n\r/', '', $dataval);
						$data[$row][$data_header[$col]] = $dataval;
					}
				}
				if (empty($data[$row]) && $row != 1) {
					break;
				}
			}
			// Save z to MongoDB
			if (!empty($data)) {
				$participant = new ParticipantTemporary;
				$participant->survey_id = $survey->id;
				$participant->delayed_job_id = $delayed_jobs->id;
				$participant->data = json_encode($data);
				$participant->save();
			}
			return $data;
	}
	public static function deleteSurvey($id)
	{
		$status = 0;
		try{
			DB::beginTransaction();

			$question_categories = array();
			$question_categories_data = DB::table('question_categories')->where('survey_id','=',$id);
			$question_categories_loads = $question_categories_data->get();
			foreach ($question_categories_loads as $question_categories_load) {
				array_push($question_categories, $question_categories_load->id);
			}

			$questions = array();
			if (count($question_categories) > 0) {
				$question_datas = DB::table('questions')->whereIn('question_category_id',$question_categories);
				$question_loads = $question_datas->get();
				foreach ($question_loads as $question_load) {
					array_push($questions, $question_load->id);
				}

				$answers = array();
				$answer_data = DB::table('answers')->whereIn('question_id',$questions);
				$answer_loads = $answer_data->get();
				foreach ($answer_loads as $answer_load) {
					array_push($answers, $answer_load->id);
				}
				$amounts = DB::table('amounts')->whereIn('answer_id',$answers)->delete();
				$amount_filters = DB::table('amount_filters')->whereIn('answer_id',$answers)->delete();

				$answer_data->delete();
				$question_datas->delete();
				$question_categories_data->delete();
			}

			$participants = array();
			$participant_data = DB::table('participants')->where('survey_id','=',$id);
			$participant_loads = $participant_data->get();
			foreach ($participant_loads as $participant_load) {
				array_push($participants, $participant_load->id);
			}
			if (count($participants) > 0) {
				$filter_participants = array();
				$filter_participant_data = DB::table('filter_participants')->whereIn('participant_id',$participants);
				$filter_participant_loads = $filter_participant_data->get();

				foreach ($filter_participant_loads as $filter_participant_load) {
					array_push($filter_participants, $filter_participant_load->category_item_id);
				}

				$category_items = array();
				$category_items_data = DB::table('category_items')->whereIn('id',$filter_participants);
				$category_items_loads = $category_items_data->get();
				foreach ($category_items_loads as $category_items_load) {
					array_push($category_items, $category_items_load->category_id);
				}
				$category = DB::table('categories')->whereIn('id',$category_items)->delete();

				$filter_participant_data->delete();
				$category_items_data->delete();
				$participant_data->delete();

				$question_participants = array();
				$question_participant_data = DB::table('question_participants')->whereIn('participant_id',$participants);
				$question_participant_loads = $question_participant_data->get();
				foreach ($question_participant_loads as $question_participant_load) {
					array_push($question_participants, $question_participant_load->region_id);
				}
				$regions = Region::whereIn('id',$question_participants)->delete();
				$question_participant_data->delete();
			}

			$master_codes = array();
			$master_codes_data = DB::table('master_codes')->where('survey_id','=',$id);
			$master_codes_loads = $master_codes_data->get();
			foreach ($master_codes_loads as $master_code) {
				array_push($master_codes, $master_code->id);
			}
			if (count($master_codes) > 0) {
				$codes = array();
				$codes_data = DB::table('codes')->whereIn('master_code_id',$master_codes);
				$codes_loads = $codes_data->get();
				foreach ($master_codes_loads as $master_code) {
					array_push($codes, $master_code->id);
				}
				$codes_data->delete();
			}


			$master_codes_data->delete();

			Survey::destroy($id);

			// Remove data in mongo
			// Emptying mongo data
			$cursors = Assign::all();
			foreach ($cursors as $key => $cursor) {
			// Delete document in collections monggodb
				$assign_delete = Assign::find(['survey_id'=>(string)$id])->first();
			// Delete actions
				$assign_delete->delete();
			}
			$cursors = Header::all();
			foreach ($cursors as $key => $cursor) {
			// Delete document in collections monggodb
				$header_delete = Header::find(['survey_id'=>(string)$id])->first();
				// Delete actions
				$header_delete->delete();
			}
			$cursors = ParticipantTemporary::all();
			foreach ($cursors as $key => $cursor) {
			// Delete document in collections monggodb
				$participant_delete = ParticipantTemporary::find(['survey_id'=>(string)$id])->first();
				// Delete actions
				$participant_delete->delete();
			}
			DB::commit();
			$status = 1;
		}
		catch(\PDOException $e){
			DB::rollback();
			$status = 0;
    }
    return $status;
	}
}