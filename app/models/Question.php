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
		'is_default'
		);
	protected $guarded = array('id');

	/* Rules */
	public static $rules = array(
		'code' => 'required',
		'code_id' => 'required',
		// 'question' => 'required',
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
	
	public static function checkData($data,$code_id,$question_category_id)
	{
		$question = Question::where('code_id', '=', $code_id)->first();
		
		if(!isset($question))
		{
			$question = Question::create(array('question' => $data, 'code_id' => $code_id, 'question_category_id' => $question_category_id));
		}	
		return $question;
	}

	public static function DefaultLoad($request)
	{
		// $questions =  DB::table('questions');

		if (!empty($request['region']))
		{
			if(!empty($request['empty']) && $request['empty'] == 1){

				$query = 'questions.id as id_question,
					questions.code_id as question_code,
					questions.question as question,
					question_categories.id as id_question_categories,
					question_categories.name as question_categories,
					regions.id as id_region,
					regions.name,
					0 AS indexlabel';	
			}else{

				$query = 'questions.id as id_question,
					questions.code_id as question_code,
					questions.question as question,
					question_categories.id as id_question_categories,
					question_categories.name as question_categories,
					answers.id  as id_answer,
					answers.answer as answer,
					regions.id as id_region,
					regions.name,
					colors.color,
					cycles.id  as id_cycle,
					cycles.cycle_type  as cycle_type,
					cycles.name as cycle,
					(SELECT sum(amounts.amount) 
						from amounts 
						where amounts.answer_id = id_answer) AS amount,
					0 AS indexlabel';	
			}
			
		}else
		{
			if(!empty($request['empty']) && $request['empty'] == 1){
				
				$query = 'questions.id as id_question,
					questions.code_id as question_code,
					questions.question as question,
					question_categories.id as id_question_categories,
					question_categories.name as question_categories,
					0 AS indexlabel';	
			}else{

				$query = 'questions.id as id_question,
						questions.code_id as question_code,
						questions.question as question,
						question_categories.id as id_question_categories,
						question_categories.name as question_categories,
						answers.id  as id_answer,
						answers.answer as answer,
						colors.color,
						cycles.id  as id_cycle,
						cycles.cycle_type  as cycle_type,
						cycles.name as cycle,
						(SELECT sum(amounts.amount) 
							from amounts 
							where amounts.answer_id = id_answer) AS amount,
						0 AS indexlabel';
			}
		}
		$questions = DB::table('questions')
					->select(DB::raw($query))
					->join('question_categories','questions.question_category_id','=','question_categories.id')
					->join('answers','answers.question_id','=','questions.id')
					->join('cycles','cycles.id','=','answers.cycle_id')
					->join('amounts','amounts.answer_id','=','answers.id')
					->join('regions','regions.id','=','amounts.region_id')
					->join('colors','answers.color_id','=','colors.id');

		$questions = $questions->where('amounts.sample_type', '=', 0);

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

				$answer_diff[$question->id_answer]->amount = 0;
			}
			$answer_diff[$question->id_answer]->amount = $question->amount;
		}

		return $answer_diff;
	}

	public static function IndexLabel($questions)
	{
		// Count question amount
		$total_amount = 0;
		foreach ($questions as $key_questions => $question) {
			$total_amount += $question->amount;
		}

		// Count index label percentage
		foreach ($questions as $key_questions => $question) {
			$question->indexlabel = !$total_amount ? 0 : round(($question->amount / $total_amount) * 100,1);
		}
		// sort array based on amounts
		usort($questions, function($a, $b) {
			return $a->amount - $b->amount;
		});
		
		return $questions;
	}

	public static function DefaultQuestion($request = array())
	{
		// Load Question
		$questions =  self::DefaultLoad($request);

			if (!empty($request['cycle'])) {
				$questions = $questions->where('answers.cycle_id', '=',$request['cycle']);
				if (!empty($request['category'])) {
					$questions =  $questions->where('question_categories.id', '=', $request['category']);
				}
				if (!empty($request['question'])) {
					$questions =  $questions->where('questions.id', '=', $request['question']);
				}
				if (!empty($request['region'])) {
					$region = $request['region'];
					$region_dapil = $request['region_dapil'];
					$questions =  $questions->where(
						function ($query) use ($region,$region_dapil) {
						$query->where('regions.name', '=', (string)$region)
						->orWhere('regions.name', '=', (string)$region_dapil);
					});
				}
			}
			else{
				$questions = $questions->where('questions.is_default', '=', 1);
			}

			if(!empty($request['cycle'])){
				$questions = $questions
				->havingRaw('min(id_answer)')
				->get();	
			}else{
				$questions = $questions
				->groupBy('answer')
				->get();
			}

			if (count($questions)) {
				// if (!empty($request['answers'])) {
				// 	if (count($questions) != count($request['answers'])) {

				$questions = self::DifferentAnswer($questions,$request);
				// 	}
				// }

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
					$region_dapil = $request['region_dapil'];
					$questions =  $questions->where(
						function ($query) use ($region,$region_dapil) {
						$query->where('regions.name', '=', (string)$region)
						->orWhere('regions.name', '=', (string)$region_dapil);
					});
				}
				if (!empty($request['cycle'])) {
					$questions =  $questions->where('answers.cycle_id', '=', $request['cycle']);
				}
			}

			$questions =  $questions
				->groupBy('answer')
				->get();
		if (count($questions)) {
			// if (!empty($request['answers'])) {
			// 	if (count($questions) != count($request['answers'])) {
			$questions = self::DifferentAnswer($questions,$request);
			// 	}
			// }

			$questions = self::IndexLabel($questions);
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
			;

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
		// If Backward
		if (($request['FilterMove'] == 1)) {
			$request['question'] =  DB::table('questions')->select('id')->whereRaw("questions.id = (select max(questions.id) from questions JOIN answers ON answers.question_id = questions.id JOIN cycles ON cycles.id = answers.cycle_id where cycles.cycle_type = 1 and questions.id < ".$request['question']." and questions.question_category_id = ". $request['category'] .")")->first();
			// If no backward
			if (!count($request['question'])) {
				$request['question'] =  DB::table('questions')->select(DB::raw('max(questions.id) as id'))
					->join('answers','answers.question_id','=','questions.id')
					->join('cycles','cycles.id','=','answers.cycle_id')
					->where("cycles.cycle_type","=",1)
					->where('questions.question_category_id', '=', $request['category'])
					->orderBy('questions.id', 'desc')->first();
			}
			$request['question'] = $request['question']->id;
		}
		// If Forward
		if (($request['FilterMove'] == 2)) {
			$request['question'] =  DB::table('questions')->select('id')->whereRaw("questions.id = (select min(questions.id) from questions JOIN answers ON answers.question_id = questions.id JOIN cycles ON cycles.id = answers.cycle_id where cycles.cycle_type = 1 and questions.id > ".$request['question']." and questions.question_category_id = ". $request['category'] .")")->first();

			// If no forard
			if (!count($request['question'])) {
				$request['question'] =  DB::table('questions')->select(DB::raw('min(questions.id) as id'))
					->join('answers','answers.question_id','=','questions.id')
					->join('cycles','cycles.id','=','answers.cycle_id')
					->where("cycles.cycle_type","=",1)
					->where('questions.question_category_id', '=', $request['category'])
					->first();
			}
			$request['question'] = $request['question']->id;
		}

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
					$region_dapil = $request['region_dapil'];
					$questions =  $questions->where(
						function ($query) use ($region,$region_dapil) {
						$query->where('regions.name', '=', (string)$region)
						->orWhere('regions.name', '=', (string)$region_dapil);
					});
				}
		}

		$questions =  $questions
		->groupBy('id_answer')
		->get();

		// print_r($questions);
		return $questions;
	}

	public static function NextQuestion($request = array())
	{
		// If Backward
		if (($request['FilterMove'] == 0)) {

			$query_raw = "questions.id = 
							(select max(questions.id) 
								from questions 
									inner join question_categories on question_categories.id=questions.question_category_id
									left join answers on answers.question_id = questions.id
									where questions.id < ".$request['question'];
								
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
					$region = $request['region'];
					$region_dapil = $request['region_dapil'];
					$questions =  $questions->where(
						function ($query) use ($region,$region_dapil) {
						$query->where('regions.name', '=', (string)$region)
						->orWhere('regions.name', '=', (string)$region_dapil);
					});
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
									left join answers on answers.question_id = questions.id
									where questions.id > ".$request['question'];
								
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
					$region = $request['region'];
					$region_dapil = $request['region_dapil'];
					$questions =  $questions->where(
						function ($query) use ($region,$region_dapil) {
						$query->where('regions.name', '=', (string)$region)
						->orWhere('regions.name', '=', (string)$region_dapil);
					});
				}
			}
			
			$query_raw .= ")";
			
			$request['question'] =  DB::table('questions')
									->select('id')
									->whereRaw($query_raw)
									->first();
			// $request['question'] =  DB::table('questions')->select('id')->whereRaw("questions.id = (select min(id) from questions where questions.id > ".$request['question'].")")->first();
									
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
					$region = $request['region'];
					$region_dapil = $request['region_dapil'];
					$questions =  $questions->where(
						function ($query) use ($region,$region_dapil) {
						$query->where('regions.name', '=', (string)$region)
						->orWhere('regions.name', '=', (string)$region_dapil);
					});
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

			

		

		return $questions;
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
					$questions =  $questions->where(
						function ($query) use ($region,$region_dapil) {
						$query->where('regions.name', '=', (string)$region)
						->orWhere('regions.name', '=', (string)$region_dapil);
					});
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
		$questions = DB::table('questions')
			->select(
				'question_categories.name as question_category',
				'questions.id as question_id',
				'questions.question as question',
				'codes.code',
				'master_codes.master_code'
				)
			->join('question_categories','questions.question_category_id','=','question_categories.id')
			->join('answers','answers.question_id','=','questions.id')
			->join('codes','codes.id','=','questions.code_id')
			->join('master_codes','master_codes.id','=','codes.master_code_id')
			->where('answers.cycle_id', '=', $request['cycle_id'])->get();

		return $questions;
	}
}