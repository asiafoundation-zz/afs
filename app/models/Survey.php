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
		'header_file',
		'publish',
		'information',
		'is_default'
		);
	protected $guarded = array('id');

	/* Rules */
	public static $rules = array(
		'name' => 'required',
		'geojson_file' => 'required|mimes:geojson',
		'baseline_file' => 'required|mimes:csv',
		'header_file' => 'required|mimes:csv',
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
			'header_file' => array(
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
					),
			'information' => array(
						'type' => 'text',
						'onIndex' => true
					),
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
						$percentage = round($queue->information);
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

	public static function importDataQuery($delayed_jobs,$survey,$master_code)
	{
		/*
		 * Load Csv File
		 */
		$status = true;
		$columns = "";
		$file_name = $survey->baseline_file;
		$inputFileName = public_path().'/uploads/'.$survey->baseline_file.".csv";
		$fp = fopen($inputFileName, 'r');
		$frow = fgetcsv($fp,0, ',');

		$schema_texts = array();
		foreach($frow as $key => $column) {
			$schema_texts[$key] = $column;
		}

		Schema::create($file_name, function($table) use ($schema_texts) {
			foreach ($schema_texts as $key => $schema_text) {
				$table->text($schema_text)->nullable();
			}
		}
		);

		$database = Config::get('database.connections.mysql.database');
		$username = Config::get('database.connections.mysql.username');
		$password = Config::get('database.connections.mysql.password');

		shell_exec("mysqlimport  --ignore-lines=1 --fields-optionally-enclosed-by='\"' --fields-terminated-by=, --local -u ".$username." -p".$password." ".$database." ".$inputFileName);

		DB::table('participants')->truncate();

		/*
		 * Mass Insert
		 */

		DB::statement("ALTER TABLE ".$file_name." ADD(participant_id int)");
		DB::statement("UPDATE ".$file_name.", (SELECT @rownum:=0) r SET participant_id = @rownum:=@rownum+1");

		DB::statement("INSERT INTO cycles(NAME, cycle_type)
			(SELECT DISTINCT sfl_wave, CASE sfl_wave WHEN 'Baseline' THEN 0 WHEN 'Endline' THEN 1 END cycle_type FROM ".$file_name.")");
		DB::statement("INSERT INTO regions(NAME, code_id)
			(SELECT DISTINCT substr(sfl_prov, 4, length(sfl_prov)),1 FROM ".$file_name.")");
		DB::statement("INSERT INTO participants(id, sample_type,survey_id)
			(SELECT participant_id, CASE substring_index(sfl_cat, '.', 1) WHEN 1 THEN 0 ELSE 1 END sample,".$survey->id." FROM ".$file_name.")");
		$count_participants = DB::table('participants')->count();

		// Progress Bar Estimations
		$delayed_jobs->information = 10;
		$delayed_jobs->save();
		// Change Status
		$survey->publish = 2;
		$survey->save();

		$sql_commands = "
		UPDATE ".$file_name." a 
		SET
		";
		$update_filter_sql = "";
		foreach ($master_code as $key_master_code => $single_code) {
			if ($single_code['type'] == 0) {
				$update_filter_sql .= "sfl_prov = (SELECT id FROM regions WHERE name = substr(a.sfl_prov, 4, length(a.sfl_prov))),";
			}
			if ($single_code['type'] == 1) {
				$update_filter_sql .= "sfl_wave = (SELECT id FROM cycles WHERE name = a.sfl_wave),";
				break;
			}
		}
		$update_filter_sql = substr_replace($update_filter_sql ,";",-1);

		$sql_commands .= $update_filter_sql;
		DB::statement($sql_commands);

		// Progress Bar Estimations
		$delayed_jobs->information = 20;
		$delayed_jobs->save();

		DB::table('answers')->truncate();
		DB::table('question_participants')->truncate();
		DB::table('filter_participants')->truncate();

		$categories = DB::table('categories')->get();
		foreach ($categories as $key_categories => $category) {
			$sql_commands = "
			INSERT INTO category_items(NAME, category_id, TYPE, `ORDER`)
			(SELECT distinct CASE IFNULL( ".$category->name.", ' ') WHEN ' ' THEN 'Not Answered' ELSE ".$category->name." END , ".$category->id.", 0,0 FROM ".$file_name.")
				";
			DB::statement($sql_commands);

			$sql_commands = "
			UPDATE ".$file_name." t
			SET ".$category->name." =
			(SELECT id FROM category_items c WHERE t.".$category->name." = c.name  AND category_id=".$category->id.")
			";
			DB::statement($sql_commands);

			$sql_commands = "
			INSERT INTO filter_participants(participant_id, category_item_id)
			(SELECT participant_id, ".$category->name." FROM ".$file_name.")
			";
			DB::statement($sql_commands);
			Log::info('Category:'.$category->name);
		}

		// Progress Bar Estimations
		$delayed_jobs->information = 30;
		$delayed_jobs->save();

		$temporary_participants = Schema::getColumnListing($file_name);
		$temporary_participants = array_flip($temporary_participants);

		foreach ($master_code as $key_answers_code => $single_code)
		{
			if($single_code['type'] == 4){
				if (!empty($temporary_participants[$single_code['code']])) {
					$question_id = DB::table('questions')->where('code_id','=',$single_code['code_id'])->first();
					$question_id = $question_id->id;

					$sql_commands = "
					INSERT INTO answers(answer, question_id, cycle_id)
					(SELECT distinct ".$single_code['code'].", ".$question_id.", sfl_wave FROM ".$file_name." WHERE ifnull(trim(".$single_code['code']."),'') != '');
					";

					DB::statement($sql_commands);

					$sql_commands = "
						UPDATE ".$file_name." t
						SET ".$single_code['code']." = (SELECT id FROM answers a
							WHERE question_id = ".$question_id." AND t.".$single_code['code']." = a.answer  AND t.sfl_wave = a.cycle_id) WHERE ".$single_code['code']."!= ''
							;";

					DB::statement($sql_commands);

					$sql_commands = "
					INSERT INTO question_participants(participant_id, answer_id, region_id)
					 (SELECT participant_id, ".$single_code['code'].", sfl_prov FROM ".$file_name." WHERE ".$single_code['code']."!= '');
					 ";
					DB::statement($sql_commands);
					Log::info('Question:'.$single_code['code']);
				}
			}
		}
		// Progress Bar Estimations
		$delayed_jobs->information = 70;
		$delayed_jobs->save();

		Log::info('amount');
		DB::statement("INSERT INTO amounts(amount, answer_id, region_id, sample_type) (SELECT count(q.participant_id), q.answer_id, q.region_id, p.sample_type FROM question_participants q JOIN participants p ON p.id = q.participant_id GROUP BY q.answer_id, q.region_id, p.sample_type);");
		// Progress Bar Estimations
		$delayed_jobs->information = 80;
		$delayed_jobs->save();

		Log::info('filters');
		DB::statement("INSERT INTO amount_filters(amount, answer_id, region_id, sample_type, category_item_id)(SELECT count(q.participant_id), q.answer_id, q.region_id, p.sample_type, f.category_item_id FROM question_participants q JOIN participants p ON p.id = q.participant_id JOIN filter_participants f ON q.participant_id = f.participant_id GROUP BY q.answer_id, q.region_id, p.sample_type, f.category_item_id)");

		// Progress Bar Estimations
		$delayed_jobs->information = 90;
		$delayed_jobs->save();

		Log::info('color_id');
		DB::statement("UPDATE answers, (SELECT @rownum:=0) r SET color_id = (CASE @rownum WHEN 30 THEN @rownum:=1 ELSE @rownum:=@rownum+1 END)");

		// Progress Bar Estimations
		$delayed_jobs->information = 95;
		$delayed_jobs->save();

		// Schema::drop('temporary_headers');
		// Schema::drop($file_name);
		return $status;
	}

	Public static function readHeaderCSV($survey,$delayed_jobs = array())
	{
		set_time_limit(0);

		// Set variable data
		$data_label = array();

		$columns = "";
		$survey = Survey::where('id', '=', $survey->id)->first();
		$inputFileName = public_path().'/uploads/'.$survey->header_file.'.csv';
		$fp = fopen($inputFileName, 'r');
		$frow = fgetcsv($fp,0, ',');

		$schema_texts = array();
		foreach($frow as $key => $column) {
			$schema_texts[$key] = $column;
		}
		Schema::create('temporary_headers', function($table) use ($schema_texts) {
			foreach ($schema_texts as $key => $schema_text) {
				$table->text($schema_text)->nullable();
			}
		}
		);

		$flag = true;
		$i = 0;
		$temporary_headers = array();
		while (($emapData = fgetcsv($fp, 10000, ",")) !== FALSE)
			if($flag) { 
				foreach($frow as $key => $column) {
					$dataval = utf8_encode((string)$emapData[$key]);
					$dataval = preg_replace('/[^A-Za-z0-9\-\s?\/#$%^&*()+=\-\[\],.:<>|]\n\r/', '', $dataval);
					$dataval = str_replace('"', "", $dataval);
					$dataval = trim(preg_replace('/\s\s+/', ' ', $dataval));

					$temporary_headers[$i][(string)$column] = $dataval;
				}
				$i++;
				continue; 
			}

		DB::table('temporary_headers')->insert(
				$temporary_headers
				);

		return $data_label;
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

		$multiple_flag = 50;Log::info($highestRow_2);

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
			if ($row == $multiple_flag) {Log::info('Row'.json_encode($data));
				// Save z to MongoDB
				if (!empty($data)) {
					$participant = new ParticipantTemporary;
					$participant->survey_id = $survey->id;
					$participant->delayed_job_id = $delayed_jobs->id;
					$participant->data = json_encode($data);
					$participant->save();
					// Reset Data
					$data = array();

					$multiple_flag +=50;
				}
			}
		}
		if (!empty($data)) {Log::info('LastRow'.json_encode($data));
			$participant = new ParticipantTemporary;
			$participant->survey_id = $survey->id;
			$participant->delayed_job_id = $delayed_jobs->id;
			$participant->data = json_encode($data);
			$participant->save();
			// Reset Data
			$data = array();
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
			// Remove Survey and file
			$survey = Survey::find($id);

			File::delete(public_path()."/uploads/".$survey->baseline_file);
			File::delete(public_path()."/uploads/".$survey->header_file);
			File::delete(public_path()."/uploads/".$survey->geojson_file);

			// Remove data in mongo
			// Emptying mongo data
			$header_delete = Header::find(['survey_id'=>(string)$survey->id])->first();
			$header_delete->delete();
			$assign_delete = Assign::find(['survey_id'=>(string)$survey->id])->first();
			$assign_delete->delete();
			$participant_delete = ParticipantTemporary::find(['survey_id'=>(string)$survey->id])->first();
			$participant_delete->delete();
			$cursors = Assign::all();

			$survey->delete();
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