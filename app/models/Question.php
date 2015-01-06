<?php
class Question extends Eloquent {

	/* Soft Delete */
	protected $softDelete = true;

	/* Eloquent */
	public $table = "questions";
	public $timestamps = true;

	/* Disabled Basic Actions */
	public static $disabledActions = array();

	/* Route */
	public $route = 'question';

	/* Mass Assignment */
	protected $fillable = array(
		'code',
		'code_id',
		'question',
		'question_category_id',
		'is_default',
		'survey_id'
		);
	protected $guarded = array('id');

	/* Rules */
	public static $rules = array(
		'code' => 'required',
		'code_id' => 'required',
		'question_category_id' => 'required',
		'is_default' => 'required'
		);

	/* Database Structure */
	public static function structure()
	{
		$fields = array(
			'code' => array(
				'type' => 'number',
				'onIndex' => true
			),
			'code_id' => array(
				'type' => 'number',
				'onIndex' => true
			),
			'question' => array(
					'type' => 'text',
					'onIndex' => true
			),
			'question_category_id' => array(
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
	
	public static function checkData($data,$code_id,$question_category_id,$survey_id)
	{
		$question = Question::where('code_id', '=', $code_id)->where('survey_id', '=', $survey_id)->first();
		
		if(!isset($question))
		{
			$question = Question::create(array('question' => $data, 'code_id' => $code_id, 'survey_id' => $survey_id,'question_category_id' => $question_category_id));
		}	
		return $question;
	}

	public static function DefaultLoad($request)
	{
		// if there's no region and answer is not empty
		$query = 'questions.id as id_question,
					questions.code_id as question_code,
					questions.question as question,
					questions.survey_id,
					question_categories.id as id_question_categories,
					question_categories.name as question_categories,
					answers.id  as id_answer,
					answers.answer as answer,
					answers.order as answer_order,
					colors.color,
					cycles.id  as id_cycle,
					cycles.cycle_type  as cycle_type,
					cycles.name as cycle,
					master_codes.id as master_code_id,
					master_codes.attribute_code as attribute_code,
					(SELECT sum(amounts.amount) 
						from amounts 
						where amounts.answer_id = id_answer
						and amounts.sample_type = 0) AS amount,
					0 AS indexlabel';

		if(!empty($request['region'])){
			$query = 'questions.id as id_question,
						questions.code_id as question_code,
						questions.question as question,
						questions.survey_id,
						question_categories.id as id_question_categories,
						question_categories.name as question_categories,
						answers.id  as id_answer,
						answers.answer as answer,
						answers.order as answer_order,
						colors.color,
						cycles.id  as id_cycle,
						cycles.cycle_type  as cycle_type,
						cycles.name as cycle,
						regions.id as id_region,
						regions.name as name,
						master_codes.id as master_code_id,
						master_codes.attribute_code as attribute_code,
						(SELECT sum(amounts.amount) 
							from amounts 
							where amounts.answer_id = id_answer and region_id = id_region
							and amounts.sample_type = 0) AS amount,
						0 AS indexlabel';	
		}
		
		if(!empty($request['empty']) && $request['empty'] == 1){

			//if there's no region and answer is empty
			$query = 'questions.id as id_question,
						questions.code_id as question_code,
						questions.question as question,
						questions.survey_id,
						question_categories.id as id_question_categories,
						question_categories.name as question_categories,
						master_codes.id as master_code_id,
						master_codes.attribute_code as attribute_code,
						0 AS indexlabel';			

			if(!empty($request['region'])){
				//if there's a region and answer is empty
				$query = 'questions.id as id_question,
							questions.code_id as question_code,
							questions.question as question,
							questions.survey_id,
							question_categories.id as id_question_categories,
							question_categories.name as question_categories,
							regions.id as id_region,
							regions.name,
							master_codes.attribute_code as attribute_code,
							master_codes.id as master_code_id,
							0 AS indexlabel';
			}
		}

		$questions = DB::table('questions')->select(DB::raw($query));

		if(!empty($request['empty']) && $request['empty'] == 1){
			if(!empty($request['region'])){
				$questions = $questions->join('question_categories','questions.question_category_id','=','question_categories.id')
										 ->join('codes','codes.id','=','questions.code_id')
										 ->join('master_codes','master_codes.id','=','codes.master_code_id')
									   ->join('answers','answers.question_id','=','questions.id')
									   ->join('amounts','amounts.answer_id','=','answers.id')
									   ->join('regions','regions.id','=','amounts.region_id');
			}else{
				$questions = $questions->join('question_categories','questions.question_category_id','=','question_categories.id');
			}
			
		}else{
			$questions = $questions->join('question_categories','questions.question_category_id','=','question_categories.id')
							->join('codes','codes.id','=','questions.code_id')
							->join('master_codes','master_codes.id','=','codes.master_code_id')
							->join('answers','answers.question_id','=','questions.id')
							->join('cycles','cycles.id','=','answers.cycle_id')
							->join('amounts','amounts.answer_id','=','answers.id')
							->join('colors','answers.color_id','=','colors.id');

			if (!empty($request['region'])) {
				$questions =  $questions->join('regions','regions.id','=','amounts.region_id');
			}

			if(!empty($request['survey_id'])){
				$questions = $questions->where('questions.survey_id', '=', $request['survey_id']);
			}

			$questions = $questions->where('amounts.sample_type', '=', 0);
						
		}

		return $questions;
	}

	public static function DifferentAnswer($questions, $request)
	{
		$answer_diff = array();
		$data_row = count($questions);

		foreach ($questions as $key_questions => $question) {
			for($a=0;$a<count($data_row);$a++){
				$answer_diff[$question->id_answer] = new stdClass();
				$answer_diff[$question->id_answer]->id_answer = $question->id_answer;
				$answer_diff[$question->id_answer]->answer = $question->answer;
				$answer_diff[$question->id_answer]->id_question = $question->id_question;
				$answer_diff[$question->id_answer]->question_code = $question->question_code;
				$answer_diff[$question->id_answer]->question = $question->question;
				$answer_diff[$question->id_answer]->id_question_categories = $question->id_question_categories;
				$answer_diff[$question->id_answer]->question_categories = $question->question_categories;
				$answer_diff[$question->id_answer]->color = $question->color;
				$answer_diff[$question->id_answer]->question_code = $question->cycle_type;
				$answer_diff[$question->id_answer]->cycle = $question->cycle;
				$answer_diff[$question->id_answer]->id_cycle = $question->id_cycle;
				$answer_diff[$question->id_answer]->id_region = !empty($question->id_region) ? $question->id_region : "";
				$answer_diff[$question->id_answer]->region_name = !empty($question->name) ? $question->name : "";
				$answer_diff[$question->id_answer]->attribute_code = $question->attribute_code;
				$answer_diff[$question->id_answer]->survey_id = $question->attribute_code;
				$answer_diff[$question->id_answer]->answer_order = $question->answer_order;

				$answer_diff[$question->id_answer]->amount = 0;
			}
			$answer_diff[$question->id_answer]->amount = $question->amount;
		}

		// print_r($answer_diff);

		return $answer_diff;
	}

	public static function IndexLabel($questions)
	{
		// Count question amount
		$total_amount = 0;
		foreach ($questions as $key_questions => $question) {
			if ($question->attribute_code) {
				$total_amount = DB::table('questions')
				->join('answers','answers.question_id','=','questions.id')
				->join('question_participants','question_participants.answer_id','=','answers.id')
				->join('participants','participants.id','=','question_participants.participant_id')
				->where('questions.id',$question->id_question)
				->where('participants.sample_type',0)
				->where('answers.cycle_id',$question->id_cycle);

				if(!empty($question->id_region)){
					$total_amount = $total_amount->where('question_participants.region_id',$question->id_region);
				}

				$total_amount = $total_amount->groupBy('question_participants.participant_id')
				// ->orderBy('answers.order')
				->get();

				$total_amount = count($total_amount);
				break;
			}else{
				$total_amount += $question->amount;
			}
		}

		// Count index label percentage
		$is_order = 0;
		foreach ($questions as $key_questions => $question) {

			if($question->answer_order != 0){
				$is_order = 1;
			}

			if (isset($question->answer)) {
				$dataval = preg_replace('/[^A-Za-z0-9\-\s?\/#$%^&*()+=\-\[\],.:<>|]\n\r/', '', $question->answer);
				$dataval = str_replace('"', "", $dataval);
				$question->answer = trim(preg_replace('/\s\s+/', ' ', $dataval));
			}else{
				$question->answer = 'Not answers';
			}
			
			$question->indexlabel = !$total_amount ? 0 : round(($question->amount / $total_amount) * 100,2);
		}
		// sort array based on amounts
		if($is_order != 1){
			usort($questions, function($a, $b) {
				return $a->amount - $b->amount;
			});	
		}
		else{
			usort($questions, function($a, $b) {
				return $b->answer_order - $a->answer_order;
			});
		}

		// print_r($questions);
		return $questions;
	}

	public static function loadRegion($request = array())
	{
		if (!empty($request['cycle'])) {

			$questions =  self::DefaultLoad($request);
			$questions = $questions->where('answers.cycle_id', '=',$request['cycle']);    
	
			if (!empty($request['category'])) {
				$questions =  $questions->where('question_categories.id', '=', $request['category']);
			}

			if (!empty($request['question'])) {
				$questions =  $questions->where('questions.id', '=', $request['question']);
			}

			if (!empty($request['region'])) {
				$region = $request['region'];
				$questions = $questions->where('regions.id', '=', $region);
			}

		}else{
			$questions = $questions->where('questions.is_default', '=', 1)
			->where('answers.cycle_default', '=', 1);
		}

		$questions = $questions
					->groupBy('answer')
					->get();

		if (count($questions)) {
			$questions = self::DifferentAnswer($questions,$request);
			$questions = self::IndexLabel($questions);
		}

		return $questions;

	}
	
	// -- FOR TESTING --
	// public static function DefaultQuestion($request = array())
	// {
	// 	// Load Question
	// 	$questions = self::DefaultLoad($request);

	// 		if (!empty($request['cycle'])) {

	// 			$questions = $questions->where('questions.id', '=', 55)
	// 			->where('answers.cycle_id', '=', 1);
	// 		}
	// 		else{
	// 			$questions = $questions->where('questions.id', '=', 55)
	// 			->where('answers.cycle_id', '=', 1);
	// 		}

	// 		$questions = $questions
	// 		->groupBy('answer')
	// 		->get();

	// 		if (count($questions)) {
	// 			$questions = self::DifferentAnswer($questions,$request);
	// 			$questions = self::IndexLabel($questions);
	// 		}

	// 	return $questions;
	// }

	public static function DefaultQuestion($request = array())
    {
        // Load Question
        $questions =  self::DefaultLoad($request);

            if (!empty($request['cycle'])) {

                if($request['empty'] == 0){
                    $questions = $questions->where('answers.cycle_id', '=',$request['cycle']);    
                }
                
                if (!empty($request['category'])) {
                    $questions =  $questions->where('question_categories.id', '=', $request['category']);
                }
                if (!empty($request['question'])) {
                    $questions =  $questions->where('questions.id', '=', $request['question']);
                }
                if (!empty($request['region'])) {
                    $region = $request['region'];
                    $region_dapil = $request['region_dapil'];
                    $questions = $questions->where('regions.id', '=', $region);
                }
            }
            else{
                $questions = $questions->where('questions.is_default', '=', 1)
                            ->where('answers.cycle_default', '=', 1);
            }

            $questions = $questions
            ->groupBy('answer')
            ->orderBy('answer_order')
            ->get();

            if (count($questions)) {
                $questions = self::DifferentAnswer($questions,$request);
                $questions = self::IndexLabel($questions);
            }

        return $questions;
    }

	public static function LoadQuestion($request = array())
	{
		// Load Question
		
		$questions =  self::DefaultLoad($request);

			if (count($request)) {
				if (!empty($request['category'])) {
					$questions =  $questions->where('question_categories.id', '=', $request['category']);
				}
				if (!empty($request['question'])) {
					$questions =  $questions->where('questions.id', '=', $request['question']);
				}
				if (!empty($request['region'])) {
					$region = $request['region'];
					$questions =  $questions->where('regions.id', '=', $region);
				}
				if (!empty($request['cycle'])) {
					if($request['empty'] == 0){
						$questions = $questions->where('answers.cycle_id', '=',$request['cycle']);	
					}
				}
			}

			if($request['empty'] == 0){
				$questions =  $questions->groupBy('answer')->get();
				if (count($questions)) {
					$questions = self::DifferentAnswer($questions,$request);
					$questions = self::IndexLabel($questions);
				}
			}else{
				$questions =  $questions->get();
			}

		// exit;
		return $questions;
	}

	public static function FilterQuestion($request = array())
	{
		// Load Question
		$questions =  DB::table('questions')
			->select(
				DB::raw(
					'questions.id as id_question,
					questions.code_id as question_code,
					questions.question as question,
					questions.survey_id,
					question_categories.id as id_question_categories,
					question_categories.name as question_categories,
					answers.id  as id_answer,
					answers.answer as answer,
					colors.color,
					cycles.id  as id_cycle,
					cycles.cycle_type  as cycle_type,
					cycles.name as cycle,
					0 AS amount,
					0 AS indexlabel'
					)
				)
			->join('question_categories','questions.question_category_id','=','question_categories.id')
			->join('answers','answers.question_id','=','questions.id')
			->join('cycles','cycles.id','=','answers.cycle_id')
			->join('colors','answers.color_id','=','colors.id')
			->where('questions.survey_id', '=', $request['survey_id']);

			if (count($request)) {
				if (!empty($request['category'])) {
					$questions =  $questions->where('question_categories.id', '=', $request['category']);
				}
				if (!empty($request['question'])) {
					$questions =  $questions->where('questions.id', '=', $request['question']);
				}
				if (!empty($request['cycle'])) {
					$questions =  $questions->where('answers.cycle_id', '=', $request['cycle']);
				}
			}

			$questions =  $questions
			->groupBy('answer')
			->get();

			// Count question amount
			$total_amount = 0;

			foreach ($questions as $key_questions => $question) {
				$question->amount = FilterParticipant::FilterOptions($question->id_answer,$request);
				$total_amount += $question->amount;
			}

			// Count index label percentage
			foreach ($questions as $key_questions => $question) {
				$question->indexlabel = round(!$total_amount ? 0 : ($question->amount / $total_amount) * 100,2);
			}

			// sort array based on amounts
			usort($questions, function($a, $b) {
				return $a->amount - $b->amount;
			});

		// Is questions not exist
		$questions = $total_amount > 0 ? $questions : array();
		
		return $questions;
	}
	public static function CompareCycle($request = array())
	{
		$question_id = $request['question'];
		$questions = array();
		$result = array();
		// If Backward
		if (($request['FilterMove'] == 1)) {
			$query = "select id as id
						FROM
						    (SELECT DISTINCT
						        q.id, c.cycle_type
						    FROM
						        questions q
						    JOIN answers a ON a.question_id = q.id
						    JOIN cycles c ON a.cycle_id = c.id
						    WHERE
						        q.id < '". $request['question'] ."'
						            and q.question_category_id = '". $request['category'] ."') question_cycles
						GROUP BY id
						HAVING count(id) > 1 
						order by id desc 
						limit 1";

			
			$result = DB::select(DB::raw($query));
			// print_r($result);
			if(!empty($result)){
				$request['question'] = $result[0];	
			}else{
				$request['question'] = array();
			}
			
			// If no backward
			if (!count($request['question'])) {
				// echo "I am here";
				$query = "select id as id
						FROM
						    (SELECT DISTINCT
						        q.id, c.cycle_type
						    FROM
						        questions q
						    JOIN answers a ON a.question_id = q.id
						    JOIN cycles c ON a.cycle_id = c.id
						    WHERE
						        q.id >= '". $question_id ."'
						            and q.question_category_id = '". $request['category'] ."') question_cycles
						GROUP BY id
						HAVING count(id) > 1 
						order by id desc 
						limit 1";

			
				$result = DB::select(DB::raw($query));

				if(!empty($result)){
					$request['question'] = $result[0];	
				}else{
					$request['question'] = array();
				}

			}
			$request['question'] = $request['question']->id;
		}
		// If Forward
		if (($request['FilterMove'] == 2)) {
			$query = "select id as id
						FROM
						    (SELECT DISTINCT
						        q.id, c.cycle_type
						    FROM
						        questions q
						    JOIN answers a ON a.question_id = q.id
						    JOIN cycles c ON a.cycle_id = c.id
						    WHERE
						        q.id > '". $request['question'] ."'
						            and q.question_category_id = '". $request['category'] ."') question_cycles
						GROUP BY id
						HAVING count(id) > 1 
						order by id asc 
						limit 1";

						
			$result = DB::select(DB::raw($query));

			if(!empty($result)){
				$request['question'] = $result[0];	
			}else{
				$request['question'] = array();
			}
			// If no forard
			if (!count($request['question'])) {
				// echo $question_id;

				$query = "select id as id
							FROM
							    (SELECT DISTINCT
							        q.id, c.cycle_type
							    FROM
							        questions q
							    JOIN answers a ON a.question_id = q.id
							    JOIN cycles c ON a.cycle_id = c.id
							    WHERE
							        q.id <= '". $question_id ."'
							            and q.question_category_id = '". $request['category'] ."') question_cycles
							GROUP BY id
							HAVING count(id) > 1 
							order by id asc 
							limit 1";

							
				$result = DB::select(DB::raw($query));

				$request['question'] = $result[0];	
			}

			$request['question'] = $request['question']->id;
		}

		// Load Question
		$questions = self::DefaultLoad($request);

		if (count($request)) {
			if (!empty($request['category'])) {
				$questions = $questions->where('question_categories.id', '=', $request['category']);
			}
			if (!empty($request['question'])) {
				$questions = $questions->where('questions.id', '=', $request['question']);
			}
			if (!empty($request['region'])) {
				$region = $request['region'];
				$questions = $questions->where('regions.id', '=', $region);
			}
		}

		// $is_cycles = $questions->groupBy('cycle_type')->get();
		$questions = $questions->groupBy('cycle_type')
			->groupBy('id_answer')
			->orderBy('answer')
			->get();
		
		return array($questions,$request);
	}

	public static function NextQuestion($request = array())
	{
		// If Backward
		if (($request['FilterMove'] == 0)) {

			$query_raw = "questions.id = 
							(select max(questions.id) 
								from questions 
									inner join question_categories on question_categories.id=questions.question_category_id
									inner join answers on answers.question_id = questions.id
									inner join amounts on amounts.answer_id = answers.id ";
			
			if (!empty($request['region'])) {
				$query_raw .= " inner join regions on regions.id = amounts.region_id ";	
			}

			$query_raw .= "where questions.id < ".$request['question'];

			if (count($request)) {
				if (!empty($request['category'])) {
					$query_raw .= " and question_categories.id = ". $request['category'];
				}
				if($request['empty'] == 0){
					if (!empty($request['cycle'])) {
						$query_raw .= " and answers.cycle_id = ". $request['cycle'];
					}
				}
				if (!empty($request['region'])) {
					$query_raw .= " and regions.id = ". (integer)$request['region'];
				}
			}

			$query_raw .= ")";

			$request['question'] =  DB::table('questions')
									->select('id')
									->whereRaw($query_raw)
									->first();
			// If no backward
			if (!count($request['question'])) {
				$request['question'] =  DB::table('questions')
										->select(DB::raw('max(questions.id) as id'))
										->join('answers', 'answers.question_id', '=', 'questions.id')
										->where("questions.question_category_id","=",$request['category'])
										->where('answers.cycle_id', '=', $request['cycle'])
										->first();
			}
			$request['question'] = $request['question']->id;
		}
		// If Forward
		if (($request['FilterMove'] == 1)) {

			$query_raw = "questions.id = 
							(select min(questions.id) 
								from questions 
									inner join question_categories on question_categories.id=questions.question_category_id
									inner join answers on answers.question_id = questions.id
									inner join amounts on amounts.answer_id = answers.id ";
			
			if (!empty($request['region'])) {
				$query_raw .= " inner join regions on regions.id = amounts.region_id ";	
			}

			$query_raw .= "where questions.id > ".$request['question'];

			if (count($request)) {
				if (!empty($request['category'])) {
					$query_raw .= " and question_categories.id = ". $request['category'];
				}
				if($request['empty'] == 0){
					if (!empty($request['cycle'])) {
						$query_raw .= " and answers.cycle_id = ". $request['cycle'];
					}
				}
				if (!empty($request['region'])) {
					$query_raw .= " and regions.id = ". (integer)$request['region'];
				}
			}
			
			$query_raw .= ")";
			
			$request['question'] =  DB::table('questions')
									->select('id')
									->whereRaw($query_raw)
									->first();
									
			// If no forard
			if (!count($request['question'])) {
				$request['question'] =  DB::table('questions')
										->select(DB::raw('min(questions.id) as id'))
										->join('answers', 'answers.question_id', '=', 'questions.id')
										->where("questions.question_category_id","=",$request['category'])
										->where('answers.cycle_id', '=', $request['cycle'])
										->first();
			}
			$request['question'] = $request['question']->id;
		}

		// Load answers
		$request['answers'] =  DB::table('questions')->select('answers.id as id','answers.answer')->join('answers','answers.question_id','=','questions.id')->where('questions.id', '=', $request['question'])->get();

		/*-- Define empty answers --*/
		$empty_question = Question::select(DB::raw('distinct questions.id'));

		if(!empty($request['region'])){
			$empty_question = $empty_question->join('answers', 'answers.question_id', '=', 'questions.id')
								->join('amounts', 'amounts.answer_id', '=', 'answers.id')
								->join('regions', 'regions.id', '=', 'amounts.region_id')
								->where('regions.id', '=', $request['region']);
		}else{
			$empty_question = $empty_question->join('answers', 'answers.question_id', '=', 'questions.id');
		}

		$empty_question = $empty_question->where('questions.id','=', $request['question'])	 
							->where('questions.question_category_id', '=', $request['category'])	 
							->first();

		$is_empty = 0;
		if(isset($empty_question)){
			$request['empty'] = 0;	 
		}else{
			$request['empty'] = 1;
			$is_empty = 1;
		}

		/*-- End --*/

		// Load Question
		$questions =  self::DefaultLoad($request);

			if (count($request)) {
				if (!empty($request['category'])) {
					$questions =  $questions->where('question_categories.id', '=', $request['category']);
				}
				if (!empty($request['question'])) {
					$questions =  $questions->where('questions.id', '=', $request['question']);
				}
				if($request['empty'] == 0){
					if (!empty($request['cycle'])) {
						$questions =  $questions->where('answers.cycle_id', '=', $request['cycle']);
					}
				}
				if (!empty($request['region'])) {
					$questions =  $questions->where('regions.id', '=', (integer)$request['region']);
				}
			}

			if($request['empty'] == 0){
				$questions = $questions->groupBy('answer')
							->get();
							
				if (count($questions)) {
					$questions = self::IndexLabel($questions);
				}
			}else{
				$questions =  $questions->get();
			}

		return array($questions, $is_empty);
	}

	public static function CompareQuestion($request = array())
	{
		// If Backward
		if (($request['FilterMove'] == 0)) {
			$request['question_move'] =  DB::table('questions')->select('id')->whereRaw("questions.id = (select max(id) from questions where questions.code_id = ".$request['question_code']." and questions.id < ".$request['question'].")")->first();
			// If no backward
			if (!count($request['question_move'])) {
				$request['question_move'] =  DB::table('questions')->select('id')->orderBy('id', 'desc')->where('code_id','=', $request['question_code'])->first();
			}
			$request['question_move'] = $request['question_move']->id;
		}
		// If Forward
		if (($request['FilterMove'] == 1)) {
			$request['question_move'] =  DB::table('questions')->select('id')->whereRaw("questions.id = (select min(id) from questions where questions.code_id = ".$request['question_code']." and questions.id > ".$request['question'].")")->first();

			// If no forard
			if (!count($request['question_move'])) {
				$request['question_move'] =  DB::table('questions')->select('id')->where('code_id','=', $request['question_code'])->first();
			}
			$request['question_move'] = $request['question_move']->id;
		}

		// Load Question
		$questions =  self::DefaultLoad($request);

		if (count($request)) {
			if (!empty($request['category'])) {
				$questions =  $questions->where('question_categories.id', '=', $request['category']);
			}
			if (!empty($request['question'])) {
				$questions =  $questions->whereRaw("(questions.id = ".$request['question']." or questions.id = ".$request['question_move'].")");
			}
			if (!empty($request['region'])) {
					$region = $request['region'];
					$region_dapil = $request['region_dapil'];
					$questions = $questions->where('regions.id', '=', $region);
				}
			if (!empty($request['cycle'])) {
				$questions =  $questions->where('answers.cycle_id', '=', $request['cycle']);
			}
		}

		$questions =  $questions
		->groupBy('id_answer')
		->get();

		return $questions;
	}

	public static function loadQuestionCycle($request=array())
	{
		$questions = Question::select(
				'question_categories.name as question_category',
				'questions.id as question_id',
				'questions.question as question',
				'questions.survey_id',
				'codes.code',
				'master_codes.master_code'
				)
			->join('question_categories','questions.question_category_id','=','question_categories.id')
			->join('answers','answers.question_id','=','questions.id')
			->join('codes','codes.id','=','questions.code_id')
			->join('master_codes','master_codes.id','=','codes.master_code_id')
			->where('answers.cycle_id', '=', $request['cycle_id'])
			->groupBy('question_id')
			->paginate();

		return $questions;
	}
}