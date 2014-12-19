<?php

class HomeController extends BaseController {

	public function getIndex()
	{
		$request = array();
		$survey = DB::table('surveys')->where('is_default','=',1)->first();

		if (!count($survey)) {
			return View::make('error.404');
		}
		if (!$survey->publish) {
			return View::make('error.404');
		}
		// Get Default Question
		$default_questions = Question::DefaultQuestion(Input::get());

		if (empty($default_questions)) {
			return View::make('error.404');
		}
		$default_question = reset($default_questions);

		$request['category'] = $default_question->id_question_categories;
		$request['cycle'] = $default_question->id_cycle;

		// Get catefory and question list
		$question_categories_query = QuestionCategory::QuestionCategoryFilterRegion($request);
		$split_data = QuestionCategory::SplitQuestionsCategory($question_categories_query);
		$question_by_category = QuestionCategory::questionByCategory($request);

		$data = array(
			"survey" => $survey,
			"filters" => Code::getFilter(),
			"cycles" => Cycle::AllCycle($survey->id),
			"question_categories" => $split_data['question_categories'],
			"question_lists" => $question_by_category,
			"default_question" => $default_question,
			"question" => $default_questions,
			"public_path" => public_path(),
			"regions" => QuestionParticipant::RegionColor($default_question->id_cycle,$default_questions),
		);
		$data["cycles_count"] = count(Cycle::QuestionCycle($default_question));
		
		return View::make('home.index', $data);
	}

	// public function filterSelect()
	// {
	// 	if(Request::ajax()){
	// 		switch (Input::get('SelectedFilter')) {
	// 			case 'cycle':
				
	// 				$default_questions = Question::DefaultQuestion(Input::get());

	// 				if (empty($default_questions)) {
	// 					return 0;
	// 				}

	// 				$default_question = reset($default_questions);

	// 				$load_filter = array(
	// 					"question" => $default_questions,
	// 					"default_question" => $default_question,
	// 					"regions" => QuestionParticipant::RegionColor($default_question->id_cycle,$default_questions),
	// 					);
	// 				$return = count($default_questions) > 0 ? $load_filter : 0;

	// 				return $return;
	// 				break;

	// 			case 'area':
	// 				$question_categories_query = QuestionCategory::QuestionCategoryFilterRegion(Input::get());
	// 				$split_data = QuestionCategory::SplitQuestionsCategory($question_categories_query);

	// 				$filter_category = (string)View::make('home.filter_category',$split_data)->render();
	// 				$filter_question = (string)View::make('home.filter_question',$split_data)->render();

	// 				$split_data = $filter_category.";".$filter_question;

	// 				$return = count($question_categories_query) > 0 ? $split_data : 0;

	// 				return $return;
	// 				break;

	// 			case 'survey':
	// 				/*-- Define empty answers --*/
	// 				$empty_question = Question::select(DB::raw('distinct questions.id'));

	// 				if(!empty(Input::get('region'))){
	// 					$empty_question = $empty_question->join('answers', 'answers.question_id', '=', 'questions.id')
	// 										->join('amounts', 'amounts.answer_id', '=', 'answers.id')
	// 										->join('regions', 'regions.id', '=', 'amounts.region_id')
	// 										->where('regions.id', '=', Input::get('region'));
	// 				}else{
	// 					$empty_question = $empty_question->join('answers', 'answers.question_id', '=', 'questions.id');
	// 				}

	// 				$empty_question = $empty_question->where('questions.id','=', Input::get('question'))	 
	// 									->where('questions.question_category_id', '=', Input::get('category'))	 
	// 									->first();

	// 				if(isset($empty_question)){
	// 					Input::merge(array('empty' => 0)); 
	// 				}else{
	//  					Input::merge(array('empty' => 1));
	// 				}
	// 				/*-- End --*/

	// 				$default_questions = Question::LoadQuestion(Input::get());
	// 				if (empty($default_questions)) {
	// 					return 0;
	// 				}

	// 				$default_question = reset($default_questions);

	// 				$cycle_data = Input::get('empty') == 0 ? Cycle::QuestionCycle($default_question) : 0;
	// 				$region_color = Input::get('empty') == 0 ? QuestionParticipant::RegionColor($default_question->id_cycle,$default_questions) : 0;
	// 				$empty_answer = Input::get('empty') == 0 ? 0 : 1;

	// 				$load_filter = array();
	// 				$load_filter = array(
	// 					"survey" => Survey::first(),
	// 					"default_question" => $default_question,
	// 					"question" => $default_questions,
	// 					"cycles" => $cycle_data,
	// 					"regions" => $region_color,
	// 					"empty_answer" => $empty_answer
	// 				);

	// 				$return = count($default_questions) > 0 ? $load_filter : 0;

	// 				return $return;
	// 				break;

	// 			case 'filters':
	// 				$default_questions = Question::FilterQuestion(Input::get());;
	// 				$load_filter = array("question" => $default_questions);
	// 				$return = count($default_questions) > 0 ? $load_filter : 0;

	// 				return $return;
	// 				break;

	// 			case 'compare_cycle':
	// 				$request = Input::get();
	// 				list($default_questions,$request) = Question::CompareCycle($request);
	// 				if (!count($default_questions) ) {
	// 					return 0;
	// 				}

	// 				$default_question = reset($default_questions);

	// 				$first_amount_total = 0;
	// 				$second_amount_total = 0;
	// 				$answer_data = array();
	// 				foreach($default_questions as $row){
	// 					if($row->cycle_type == 0){
	// 						$first_amount_total += $row->amount;
	// 					}

	// 					if($row->cycle_type == 1){
	// 						$second_amount_total += $row->amount;	
	// 					}
	// 				}

	// 				$baseline = array();
	// 				$endline = array();
	// 				$first_label = "";
	// 				$second_label = "";
	// 				foreach($default_questions as $row){
	// 					$answer = strtolower($row->answer);
	// 					$answer = preg_replace('/[^A-Za-z0-9]/', '', $answer);
	// 					$answer = preg_replace('/\s+/', '', $answer);
	// 					$answer = trim(preg_replace('/\s\s+/', ' ', $answer));

	// 					if ($answer == '') {
	// 						$row->answer = 'Not Answer';
	// 						$answer = 'not_answer';
	// 					}

	// 					if($row->cycle_type == 0){
	// 						$first_label = $baseline[$answer]['baseline']['cycle_type'] = $row->cycle;

	// 						$baseline[$answer]['baseline']['amount'] = !$first_amount_total ? 0 : round(($row->amount / $first_amount_total) * 100,2);
	// 						$baseline[$answer]['baseline']['cycle_type'] = $row->cycle;
	// 						$baseline[$answer]['baseline']['answer'] = trim(preg_replace('/\s\s+/', ' ', $row->answer));
	// 					}

	// 					if($row->cycle_type == 1){
	// 						$second_label = $endline[$answer]['endline']['cycle_type'] = $row->cycle;

	// 						$endline[$answer]['endline']['amount'] = !$first_amount_total ? 0 : round(($row->amount / $second_amount_total) * 100,2);
	// 						$endline[$answer]['endline']['cycle_type'] = $row->cycle;
	// 						$endline[$answer]['endline']['answer'] = trim(preg_replace('/\s\s+/', ' ', $row->answer));
	// 					}
	// 				}

	// 				$answer_normalizes = array_merge_recursive($baseline,$endline);
	// 				$answer_data = array();
	// 				$first_index = 0;
	// 				$second_index = 0;
	// 				foreach ($answer_normalizes as $key => $answer_normalize) {

	// 					if (!empty($answer_normalize['baseline'])) {
	// 						$answer_data['first_data'][$first_index]['amount'] = $answer_normalize['baseline']['amount'];
	// 						$answer_data['first_data'][$first_index]['answer'] = $answer_normalize['baseline']['answer'];
	// 						$answer_data['first_data'][$first_index]['cycle'] = $answer_normalize['baseline']['cycle_type'];

	// 						if (empty($answer_normalize['endline'])) {
	// 							$answer_data['second_data'][$second_index]['amount'] = 0;
	// 							$answer_data['second_data'][$second_index]['answer'] = $answer_normalize['baseline']['answer'];
	// 							$answer_data['second_data'][$second_index]['cycle'] = $second_label;
	// 							$second_index++;
	// 						}
	// 						$first_index ++;
	// 					}
	// 					if (!empty($answer_normalize['endline'])) {
	// 						$answer_data['second_data'][$second_index]['amount'] = $answer_normalize['endline']['amount'];
	// 						$answer_data['second_data'][$second_index]['answer'] = $answer_normalize['endline']['answer'];
	// 						$answer_data['second_data'][$second_index]['cycle'] = $answer_normalize['endline']['cycle_type'];

	// 						if (empty($answer_normalize['baseline'])) {
	// 							$answer_data['first_data'][$first_index]['amount'] = 0;
	// 							$answer_data['first_data'][$first_index]['answer'] = $answer_normalize['endline']['answer'];
	// 							$answer_data['first_data'][$first_index]['cycle'] = $first_label;
	// 							$first_index++;
	// 						}
	// 						$second_index ++;
	// 					}
	// 				}

	// 				$load_filter = array();
	// 				$load_filter = array(
	// 					"default_question" => $default_question,
	// 					"question" => $answer_data,
	// 					"cycles" => Cycle::QuestionCycle($default_question),
	// 				);

	// 				$return = count($default_questions) > 0 ? $load_filter : 0;

	// 				return $return;
	// 				break;

	// 			case 'next_question':
	// 				/*-- Define empty answers --*/
	// 				$empty_question = Question::select(DB::raw('distinct questions.id'));

	// 				if(!empty(Input::get('region'))){
	// 					$empty_question = $empty_question->join('answers', 'answers.question_id', '=', 'questions.id')
	// 										->join('amounts', 'amounts.answer_id', '=', 'answers.id')
	// 										->join('regions', 'regions.id', '=', 'amounts.region_id')
	// 										->where('regions.id', '=', Input::get('region'));
	// 				}else{
	// 					$empty_question = $empty_question->join('answers', 'answers.question_id', '=', 'questions.id');
	// 				}

	// 				$empty_question = $empty_question->where('questions.id','=', Input::get('question'))	 
	// 									->where('questions.question_category_id', '=', Input::get('category'))	 
	// 									->first();

	// 				if(isset($empty_question)){
	// 					Input::merge(array('empty' => 0));	 
	// 				}else{
	//  					Input::merge(array('empty' => 1));
	// 				}
	// 				/*-- End --*/

	// 				$default_questions = Question::NextQuestion(Input::get());
	// 				if (empty($default_questions[0])) {
	// 					return 0;
	// 				}

	// 				/*-- Define condition for empty answer --*/
	// 				$empty_answer = 0; 
	// 				if($default_questions[1] == 0){	 
	// 					$region_color = QuestionParticipant::RegionColor(0,$default_questions[0]);	 
	// 				}elseif($default_questions[1] == 1){	 
	// 					$region_color = 0;	 
	// 					$empty_answer = 1;
	// 				}
	// 				/*-- End --*/

	// 				/*-- Inisiate compare availability --*/
	// 				$compare_available = 0;
	// 				$compare = Question::select(DB::raw('questions.id, cycles.cycle_type'))
	// 							->join('answers', 'answers.question_id', '=', 'questions.id')
	// 							->join('cycles', 'cycles.id', '=', 'answers.cycle_id')
	// 							->where('questions.id', '=', $default_questions[0][0]->id_question)
	// 							->where('questions.question_category_id', '=', Input::get('category'))
	// 							->groupBy('cycles.cycle_type')
	// 							->get()
	// 							->toArray();

	// 				if(count($compare) == 2){
	// 					$compare_available = 1;
	// 				}
	// 				/*-- End --*/

	// 				$default_question = reset($default_questions);
	// 				$load_filter = array(
	// 					"survey" => Survey::first(),
	// 					"default_question" => $default_question[0],
	// 					"question" => $default_questions[0],
	// 					"regions" => $region_color,
	// 					"compare_available" => $compare_available,
	// 					"empty_answer" => $empty_answer
	// 				);

	// 				$return = count($default_questions) > 0 ? $load_filter : 0;

	// 				return $return;
	// 				break;

	// 			case 'survey_area_dynamic':
	//  				$default_questions = Question::loadRegion(Input::get());
	// 				if (empty($default_questions)) {
	// 					return 0;
	// 				}
	// 				$default_question = reset($default_questions);

	// 				$load_filter = array(
	// 					"default_question" => $default_question,
	// 					"question" => $default_questions,
	// 				);

	// 				$return = count($default_questions) > 0 ? $load_filter : 0;

	// 				return $return;
	// 				break;

	// 			case 'detail_chart':
	// 				$default_questions = Category::DetailParticipant(Input::get());
	// 				if (empty($default_questions)) {
	// 					return 0;
	// 				}
	// 				$default_question = reset($default_questions);

	// 				$load_filter = array(
	// 					"default_question" => $default_question,
	// 					"question" => $default_questions,
	// 				);

	// 				$return = count($default_questions) > 0 ? $load_filter : 0;

	// 				return $return;
	// 				break;

	// 			case 'compare_all_cycle':
	// 				$default_questions = Question::CompareCycle(Input::get());

	// 				$return = count($default_questions) > 0 ? $default_questions : 0;

	// 				return $return;
	// 				break;

	// 			case 'change_question':
	// 				$question_categories_query = QuestionCategory::QuestionCategoryFilterRegion(Input::get());
	// 				$split_data = QuestionCategory::SplitQuestionsCategory($question_categories_query);

	// 				$filter_question = (string)View::make('home.filter_question',$split_data)->render();

	// 				$split_data = $filter_question;

	// 				$return = count($question_categories_query) > 0 ? $split_data : 0;

	// 				return $return;
	// 				break;

	// 			case 'loadcategory':
	// 				$question = Question::select(DB::raw('distinct questions.id, questions.question'))
	// 							->join('answers','answers.question_id', '=', 'questions.id')
	// 							->join('amounts', 'amounts.answer_id', '=', 'answers.id')
	// 							->where('question_category_id', '=', Input::get('category'))
	// 							->where('answers.cycle_id', '=', Input::get('cycle'))
	// 							->where('amounts.sample_type', '=', 0)
	// 							->get();

	// 				$empty_question = Question::select(DB::raw('distinct min(questions.id) as id, questions.question'))
	// 							->leftJoin('answers','answers.question_id', '=', 'questions.id')
	// 							->where('question_category_id', '=', Input::get('category'))	 
	// 							->where('answers.cycle_id', '=', Input::get('cycle'))
	// 							->first();

	// 				$question_exist = isset($empty_question) ? $empty_question->id : 0;

	// 				return Response::json(array($question, $question_exist));
	// 				break;

	// 			default:
	// 				return 0;
	// 				break;
	// 		}
	// 	}

	// 	return 0;
	// }
}