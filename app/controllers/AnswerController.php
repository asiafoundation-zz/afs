<?php

class AnswerController extends AvelcaController {

	public function __construct(\Answer $Model)
	{
		parent::__construct($Model);
	}

	public function postCross()
	{
		$question_headers = Answer::select(DB::raw('count(answers.answer) as count_header, questions.question as question, questions.id as question_id, answers.id as id, answers.answer as answer'))
							->join('questions', 'questions.id', '=', 'answers.question_id')
							->where('questions.id', '=', Input::get('question_header'))
							->groupBy('answers.answer')
							->get();
							
		$query = "answers.answer as answer,";
		$counter = 0;
		foreach($question_headers as $header){
			$query .= "(
							count(answers.answer) + ". $header->count_header ."
						) as 'result$counter',";

			$counter ++;
		}

		$query = rtrim($query, ',');

		$question_rows = Answer::select(
							DB::raw($query)
						)
						->join('questions', 'questions.id', '=', 'answers.question_id')
						->where('questions.id', '=', Input::get('question_row'))
						->groupBy('answer')
						->get()->toArray();
		
		return array('question_rows' => $question_rows, 'question_headers' => $question_headers);
	}
		
}