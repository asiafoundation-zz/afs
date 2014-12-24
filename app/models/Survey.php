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
		'url',
		'url_name',
		'is_default'
		);
	protected $guarded = array('id');

	/* Rules */
	public static $rules = array(
		'name' => 'required',
		//'geojson_file' => 'required|mimes:geojson',
		'baseline_file' => 'required|mimes:csv',
		'header_file' => 'required|mimes:csv',
		'publish' => 'required',
		'url' => 'required',
		'url_name' => 'required',
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
			'url' => array(
						'type' => 'text',
						'onIndex' => true
					),
			'url_name' => array(
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
					$queue = DelayedJob::where('survey_id','=',$survey_list->id)->first();

					$surveys[$key_survey_lists]['publish_text'] = "Importing ";
					$surveys[$key_survey_lists]['publish_style'] = "importing";
					$surveys[$key_survey_lists]['percentage'] = $queue->information;
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

		/*
		 * Mass Insert
		 */

		DB::statement("ALTER TABLE ".$file_name." ADD(participant_id int)");

		$last_participant = DB::table('participants')->orderBy('id', 'desc')->first();
		$last_participant = empty($last_participant) ? 0 : $last_participant->id;

		DB::statement("UPDATE ".$file_name.", (SELECT @rownum:=".$last_participant.") r SET participant_id = @rownum:=@rownum+1");

		DB::statement("INSERT INTO participants(id, sample_type,survey_id)
			(SELECT participant_id, CASE substring_index(sfl_cat, '.', 1) WHEN 1 THEN 0 ELSE 1 END sample,".$survey->id." FROM ".$file_name.")");

		// Progress Bar Estimations
		$delayed_jobs->information = "Saving Regions";
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
				$update_filter_sql .= "sfl_prov = (SELECT id FROM regions WHERE name = sfl_prov and survey_id = ".$survey->id."),";

				DB::statement("INSERT INTO regions(NAME, code_id,survey_id)
					(SELECT DISTINCT sfl_prov,".$single_code["code_id"].",".$survey->id." FROM ".$file_name.")");
			}
			if ($single_code['type'] == 1) {

				$distinct_cycles = DB::select(DB::raw("SELECT DISTINCT sfl_wave, CASE sfl_wave WHEN 'Baseline' THEN 0 WHEN 'Endline' THEN 1 END cycle_type FROM ".$file_name." LIMIT 1"));

				$distinct_cycles = reset($distinct_cycles);

				DB::statement("INSERT INTO cycles(NAME, cycle_type,survey_id)
					(SELECT DISTINCT sfl_wave, CASE sfl_wave WHEN '".$distinct_cycles->sfl_wave."' THEN 0 ELSE 1 END cycle_type,".$survey->id." FROM ".$file_name.")");

				$update_filter_sql .= "sfl_wave = (SELECT id FROM cycles WHERE name = a.sfl_wave and survey_id = ".$survey->id."),";
				break;
			}
		}
		$update_filter_sql = substr_replace($update_filter_sql ,";",-1);

		$sql_commands .= $update_filter_sql;
		DB::statement($sql_commands);

		// Progress Bar Estimations
		$delayed_jobs->information = "Saving Categories";
		$delayed_jobs->save();

		$categories = DB::table('categories')->where('survey_id','=',$survey->id)->get();
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
			INSERT INTO filter_participants(participant_id, category_item_id,survey_id)
			(SELECT participant_id, ".$category->name.",".$survey->id." FROM ".$file_name.")
			";
			DB::statement($sql_commands);
			Log::info('Category:'.$category->name);
		}

		$temporary_participants = Schema::getColumnListing($file_name);
		$temporary_participants = array_flip($temporary_participants);

		foreach ($master_code as $key_answers_code => $single_code)
		{
			if($single_code['type'] == 4){
				if (!empty($temporary_participants[$single_code['code']])) {

					$delayed_jobs->information = "Saving ".$single_code['code'];
					$delayed_jobs->save();

					$question_id = DB::table('questions')->where('code_id','=',$single_code['code_id'])->first();
					$question_id = $question_id->id;

					$sql_commands = "
					INSERT INTO answers(answer, question_id, cycle_id,survey_id)
					(SELECT distinct ".$single_code['code'].", ".$question_id.", sfl_wave,".$survey->id." FROM ".$file_name." WHERE ifnull(trim(".$single_code['code']."),'') != '');
					";

					DB::statement($sql_commands);

					$sql_commands = "
						UPDATE ".$file_name." t
						SET ".$single_code['code']." = (SELECT id FROM answers a
							WHERE question_id = ".$question_id." AND survey_id = ".$survey->id." AND t.".$single_code['code']." = a.answer  AND t.sfl_wave = a.cycle_id) WHERE ".$single_code['code']." != ''
							;";

					DB::statement($sql_commands);

					$sql_commands = "
					INSERT INTO question_participants(participant_id, answer_id, region_id,survey_id)
					 (SELECT participant_id, ".$single_code['code'].", sfl_prov,".$survey->id." FROM ".$file_name." WHERE ".$single_code['code']." != '');
					 ";
					DB::statement($sql_commands);
					Log::info('Question:'.$single_code['code']);
				}
			}
		}

		// Multi Questions
		// Progress Bar Estimations
		$delayed_jobs->information = "Multi Questions";
		$delayed_jobs->save();

		$multi_questions = DB::select(DB::raw("SELECT questions.id as question_id, questions.question as question, questions.question_category_id as question_category_id FROM questions WHERE question IN (SELECT question FROM questions WHERE survey_id = ".$survey->id." GROUP BY question, question_category_id HAVING COUNT(question) > 1)"));

		if (count($multi_questions)) {
			$question_deletes = array();
			$code_deletes = array();
			$mastercode_deletes = array();
			$answer_deletes = array();

			$data_answers = array();
			$first_questions = array();
			foreach ($multi_questions as $key => $value) {
				$question = preg_replace('/[^A-Za-z0-9]/', '', $value->question);
				$question = strtolower($question);
				$question = preg_replace('/\s+/', '', $question);
				$question = trim(preg_replace('/\s\s+/', ' ', $question));

				$answers = DB::table('answers')
					->select('answers.question_id as question_id','answers.id as answer_id','answers.answer','cycles.cycle_type','cycles.id as cycle_id')
					->join('cycles','cycles.id','=','answers.cycle_id')
					->where('question_id','=',$value->question_id)
					->get();

				if(count($answers) > 0){
					$key_answers = 0;
					foreach ($answers as $key_answers => $answer) {
						$answer_text = preg_replace('/[^A-Za-z0-9]/', '', $answer->answer);
						$answer_text = strtolower($answer_text);
						$answer_text = preg_replace('/\s+/', '', $answer_text);
						$answer_text = trim(preg_replace('/\s\s+/', ' ', $answer_text));

						$data_answers["category".$value->question_category_id.$question][$answer_text][$answer->cycle_type][$answer->answer_id]['question_id'] = $value->question_id;
						$data_answers["category".$value->question_category_id.$question][$answer_text][$answer->cycle_type][$answer->answer_id]['answer_id'] = $answer->answer_id;
						$data_answers["category".$value->question_category_id.$question][$answer_text][$answer->cycle_type][$answer->answer_id]['answer'] = $answer->answer;
						$data_answers["category".$value->question_category_id.$question][$answer_text][$answer->cycle_type][$answer->answer_id]['cycle_id'] = $answer->cycle_id;
						if (empty($first_questions["category".$value->question_category_id.$question])) {
							$first_questions["category".$value->question_category_id.$question] = $value->question_id;
						}
					}
				}else{
					array_push($question_deletes, $value->question_id);

					$questions = DB::table('questions')->select('codes.id as code_id','master_codes.id as master_code_id')
					->join('codes','codes.id','=','questions.code_id')
					->join('master_codes','master_codes.id','=','codes.master_code_id')
					->where('questions.id','=',$value->question_id)
					->get();

					foreach ($questions as $key_questions => $question_single) {
						array_push($code_deletes, $question_single->code_id);
						array_push($mastercode_deletes, $question_single->master_code_id);
					}
				}
			}

			$answer_savings = array();
			$first_answer_savings = array();
			$answer_id_data = "";
			$question_id_data = "";
			foreach ($data_answers as $key_data_answers => $data_answer) {
				foreach ($data_answer as $key_data_answer => $value_cycle) {
					if (!empty($value_cycle[0])) {
						// Normalize Array
						$value = array_values($value_cycle[0]);
						
						for ($i=0; $i < count($value); $i++) {
							$questions = DB::table('questions')->select('codes.id as code_id','master_codes.id as master_code_id')
									->join('codes','codes.id','=','questions.code_id')
									->join('master_codes','master_codes.id','=','codes.master_code_id')
									->where('questions.id','=',$value[$i]['question_id'])
									->get();

							if ($i == 0) {
								$first_answer = $value[$i]['answer_id'];

								if ($first_questions[$key_data_answers] != $value[$i]['question_id']) {
									$first_answer_savings[$first_answer]['question_id'] = $first_questions[$key_data_answers];
									$first_answer_savings[$first_answer]['answer_id'] = $first_answer;
									$question_id_data .= $first_answer.",";
								}
								if (!empty($questions)) {
									$question = reset($questions);
									DB::table('master_codes')->where('id', $question->master_code_id)->update(array('attribute_code' => 1));
								}
							}else{
								$answer_savings[$value[$i]['answer_id']]['answer_id'] = $first_answer;
								$answer_savings[$value[$i]['answer_id']]['answer'] = $value[$i]['answer'];
								$answer_savings[$value[$i]['answer_id']]['question_id'] = $first_questions[$key_data_answers];
								$answer_savings[$value[$i]['answer_id']]['cycle_id'] = $value[$i]['cycle_id'];
								$answer_id_data .= $value[$i]['answer_id'].",";

								array_push($question_deletes, $value[$i]['question_id']);
								array_push($answer_deletes, $value[$i]['answer_id']);

								foreach ($questions as $key_questions => $question_single) {
									array_push($code_deletes, $question_single->code_id);
									array_push($mastercode_deletes, $question_single->master_code_id);
								}
							}
						}
					}
					if (!empty($value_cycle[1])) {
						// Normalize Array
						$value = array_values($value_cycle[1]);

						for ($j=0; $j < count($value); $j++) {
							$questions = DB::table('questions')->select('codes.id as code_id','master_codes.id as master_code_id')
									->join('codes','codes.id','=','questions.code_id')
									->join('master_codes','master_codes.id','=','codes.master_code_id')
									->where('questions.id','=',$value[$j]['question_id'])
									->get();

							if ($j == 0) {
								$first_answer = $value[$j]['answer_id'];

								if ($first_questions[$key_data_answers] != $value[$j]['question_id']) {
									$first_answer_savings[$first_answer]['question_id'] = $first_questions[$key_data_answers];
									$first_answer_savings[$first_answer]['answer_id'] = $first_answer;
									$question_id_data .= $first_answer.",";
								}

								if (!empty($questions)) {
									$question = reset($questions);
									DB::table('master_codes')->where('id', $question->master_code_id)->update(array('attribute_code' => 1));
								}
							}else{
								$answer_savings[$value[$j]['answer_id']]['answer_id'] = $first_answer;
								$answer_savings[$value[$j]['answer_id']]['answer'] = $value[$j]['answer'];
								$answer_savings[$value[$j]['answer_id']]['question_id'] = $first_questions[$key_data_answers];
								$answer_savings[$value[$j]['answer_id']]['cycle_id'] = $value[$j]['cycle_id'];
								$answer_id_data .= $value[$j]['answer_id'].",";

								array_push($question_deletes, $value[$j]['question_id']);
								array_push($answer_deletes, $value[$j]['answer_id']);

								$questions = DB::table('questions')->select('codes.id as code_id','master_codes.id as master_code_id')
									->join('codes','codes.id','=','questions.code_id')
									->join('master_codes','master_codes.id','=','codes.master_code_id')
									->where('questions.id','=',$value[$j]['question_id'])
									->get();

								foreach ($questions as $key_questions => $question_single) {
									array_push($code_deletes, $question_single->code_id);
									array_push($mastercode_deletes, $question_single->master_code_id);
								}
							}
						}
					}
				}
			}
			$question_deletes = array_unique($question_deletes);
			$answer_deletes = array_unique($answer_deletes);
			$code_deletes = array_unique($code_deletes);
			$mastercode_deletes = array_unique($mastercode_deletes);

			$question_deletes = array_values($question_deletes);
			$answer_deletes = array_values($answer_deletes);
			$code_deletes = array_values($code_deletes);
			$mastercode_deletes = array_values($mastercode_deletes);

			$answer_id_data .= rtrim($answer_id_data, ',');
			$question_id_data .= rtrim($question_id_data, ',');

			if (!empty($first_answer_savings)) {
				$question_answer_savings = "UPDATE answers SET question_id = CASE id";

				foreach ($first_answer_savings as $first_answer => $first_answer_saving) {
					$question_answer_savings .= " WHEN ".$first_answer_saving['answer_id']." THEN ".$first_answer_saving['question_id'];
				}
				$question_answer_savings .= " END";
				$question_answer_savings .= " WHERE id IN (".$question_id_data.")";

				DB::statement($question_answer_savings);
			}

			if (!empty($answer_id_data)) {
				$answer_data_text = "UPDATE question_participants SET answer_id = CASE answer_id";

				foreach ($answer_savings as $first_answer => $answer_saving) {
					$answer_data_text .= " WHEN ".$first_answer." THEN ".$answer_saving['answer_id'];
				}
				$answer_data_text .= " END";
				$answer_data_text .= " WHERE answer_id IN (".$answer_id_data.")";
				DB::statement($answer_data_text);
				DB::table('answers')->whereIn('id', $answer_deletes)->delete();
			}
			
			if(count($question_deletes) > 0){
				DB::table('questions')->whereIn('id', $question_deletes)->delete();
			}
			if(count($code_deletes) > 0){
				DB::table('codes')->whereIn('id', $code_deletes)->delete();
			}
			if (count($mastercode_deletes) > 0) {
				DB::table('master_codes')->whereIn('id', $mastercode_deletes)->delete();
			}
		}

		// Progress Bar Estimations
		$delayed_jobs->information = "Saving Amounts";
		$delayed_jobs->save();

		Log::info('amount');
		DB::statement("INSERT INTO amounts(amount, answer_id, region_id, sample_type,survey_id) (SELECT count(q.participant_id), q.answer_id, q.region_id, p.sample_type,".$survey->id." FROM question_participants q JOIN participants p ON p.id = q.participant_id WHERE q.survey_id = ".$survey->id." GROUP BY q.answer_id, q.region_id, p.sample_type);");
		// Progress Bar Estimations
		$delayed_jobs->information = "Saving Filters";
		$delayed_jobs->save();

		Log::info('filters');
		DB::statement("INSERT INTO amount_filters(amount, answer_id, region_id, sample_type, category_item_id,survey_id)(SELECT count(q.participant_id), q.answer_id, q.region_id, p.sample_type, f.category_item_id,".$survey->id." FROM question_participants q JOIN participants p ON p.id = q.participant_id JOIN filter_participants f ON q.participant_id = f.participant_id WHERE q.survey_id = ".$survey->id." GROUP BY q.answer_id, q.region_id, p.sample_type, f.category_item_id)");

		// Progress Bar Estimations
		$delayed_jobs->information = "Saving Colors";
		$delayed_jobs->save();

		Log::info('color_id');
		DB::statement("UPDATE answers, (SELECT @rownum:=0) r SET color_id = (CASE @rownum WHEN 30 THEN @rownum:=1 ELSE @rownum:=@rownum+1 END) WHERE survey_id= ".$survey->id."");

		// Progress Bar Estimations
		$delayed_jobs->information = "Almost Done, Please Wait....";
		$delayed_jobs->save();

		Schema::drop('temporary_headers');
		Schema::drop($file_name);
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

	public static function deleteSurvey($id)
	{
		$status = 0;
		try{
			DB::beginTransaction();

			// Remove Survey and file
			$survey = Survey::find($id);

			File::delete(public_path()."/uploads/".$survey->baseline_file);
			File::delete(public_path()."/uploads/".$survey->header_file);
			File::delete(public_path()."/uploads/".$survey->geojson_file);

			DB::statement("DROP TABLE IF EXISTS `temporary_headers`;");
			DB::statement("DROP TABLE IF EXISTS ".$survey->baseline_file.";");

			DB::table('delayed_jobs')->where('survey_id','=',$survey->id)->delete();
			DB::table('regions')->where('survey_id','=',$survey->id)->delete();
			DB::table('cycles')->where('survey_id','=',$survey->id)->delete();
			DB::table('amounts')->where('survey_id','=',$survey->id)->delete();
			DB::table('amount_filters')->where('survey_id','=',$survey->id)->delete();
			DB::table('answers')->where('survey_id','=',$survey->id)->delete();

			$category_data = DB::table('categories')->where('survey_id','=',$survey->id);
			$category_data_loads = $category_data->get();

			if (!empty($category_data_loads)) {
				$categories = array();
				foreach ($category_data_loads as $key_category_data_loads => $category_data_load) {
					array_push($categories, $category_data_load->id);

					$category_items = array();
					$category_item_datas = DB::table('category_items')->where('category_id', $category_data_load->id)->get();
					foreach ($category_item_datas as $key => $category_item_data) {
						array_push($category_items, $category_item_data->id);
					}
					DB::table('category_items')->whereIn('id', $category_items)->delete();
				}
				$category_data->delete();
			}

			$master_codes = array();
			$master_codes_data = DB::table('master_codes')->where('survey_id','=',$id);
			$master_codes_loads = $master_codes_data->get();

			if (!empty($master_codes_loads)) {
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
			}

			DB::table('filter_participants')->where('survey_id','=',$survey->id)->delete();
			DB::table('participants')->where('survey_id','=',$survey->id)->delete();
			DB::table('questions')->where('survey_id','=',$survey->id)->delete();
			DB::table('question_categories')->where('survey_id','=',$survey->id)->delete();
			DB::table('question_participants')->where('survey_id','=',$survey->id)->delete();

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