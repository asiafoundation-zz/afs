<?php

class HomeController extends BaseController {

	public function getIndex()
	{
		$default_questions = Question::LoadQuestion(Input::get());
		$default_question = reset($default_questions);

		$data = array(
			"survey" => Survey::find(1),
			"filters" => Code::getFilter(),
			"cycles" => Cycles::select('id','name')->get(),
			"question_categories" => QuestionCategory::select('id','name')->get(),
			"question_lists" => Question::select('id','question')->get(),
			"default_question" => $default_question,
			"question" => $default_questions,
			"regions" => Region::RegionColor(),
		);
    if(Request::ajax()){
      return View::make('home.survey_pemilu',$data);
    }

		return View::make('home.index', $data);
	}

	public function filterSelect()
	{
		$selected_question = Question::DefaultQuestion(Input::get());

		return View::make('home.index', $data);
	}
}