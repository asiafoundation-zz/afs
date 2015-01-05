<?php

class AnswerController extends AvelcaController {

	public function __construct(\Answer $Model)
	{
		parent::__construct($Model);
	}

	public function postCross()
	{
		$heder = Input::get('question_header');
		$row = Input::get('question_row');
		
		//Count header
		$question_header_count = Answer::join('questions', 'questions.id', '=', 'answers.question_id')
							->where('questions.id', '=', $heder)
							->groupBy('answers.answer')
							->get()
							->count();
		
		for($a=0;$a<$question_header_count;$a=$a+3){

			//Create query for question header
			$question_headers = Answer::select(DB::raw('amounts.amount as count_header, questions.question as question, questions.id as question_id, answers.id as id, answers.answer as answer'))
							->join('questions', 'questions.id', '=', 'answers.question_id')
							->join('amounts', 'amounts.answer_id', '=', 'answers.id')
							->where('questions.id', '=', $heder)
							->groupBy('answers.answer')
							->skip($a)
							->take(3)
							->get();

			$query = "answers.answer as answer,";
			$counter = 0;
			foreach($question_headers as $header){
				$query .= "(count(answers.answer) + ". $header->count_header .") as 'result$counter',";

				$counter ++;
			}

			$query = rtrim($query, ',');

			//Create query for question row
			$question_rows = Answer::select(
								DB::raw($query)
							)
							->join('questions', 'questions.id', '=', 'answers.question_id')
							->join('amounts', 'amounts.answer_id', '=', 'answers.id')
							->where('questions.id', '=', $row)
							->groupBy('answer')
							->get()->toArray();	

			$arr_row[] = $question_rows;
			$arr_header[] = $question_headers->toArray();
		}
		
		return array('question_rows' => $arr_row, 'question_headers' => $arr_header);
	}
	public function getAnswer($id)
	{
		$answer = Answer::where('question_id','=',$id)->where('cycle_id','=',Input::get('cycle_id'));
		// Is ordered
		$is_ordered = DB::table('answers')->where('question_id','=',$id)->where('cycle_id','=',Input::get('cycle_id'))->orderBy('order', 'ASC')->first();

		if ($is_ordered->order != 0) {
			$answers = $answer->orderBy('order', 'ASC')->get();
		}else{
			$answers = $answer->get();
		}

		$view = View::make('admin.survey.answer_order')->with('answers', $answers);
		$view =(string) $view;

		return $view;
	}
	public function postAnswer()
	{
		$requests = Input::get();

		foreach ($requests['order'] as $key_requests => $request) {
			DB::table('answers')->where('id', $key_requests)->update(array('order' => $request));
		}

		Session::flash('alert-class', 'alert-success'); 
		Session::flash('message', 'Save Succeed');
		return Redirect::to('admin/survey/cycle?cycle_id=1&survey_id='.$requests['survey_id']);
	}
}