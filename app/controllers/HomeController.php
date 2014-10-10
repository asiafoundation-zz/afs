<?php

class HomeController extends BaseController {

	public function getIndex()
	{
		$default_question = Question::DefaultQuestion();

		$data = array(
			"survey" => Survey::find(1),
			"filters" => Code::getFilter(),
			"cycles" => Cycles::select('id','name')->get(),
			"question_categories" => QuestionCategory::select('id','name')->get(),
			"question_lists" => Question::select('id','question')->get(),
			"default_question" => $default_question[0]->question,
			"question" => $default_question,
			"regions" => Region::RegionColor(),
		);

		return View::make('home.index', $data);
	}
}