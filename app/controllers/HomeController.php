<?php

class HomeController extends BaseController {

	public function getIndex()
	{
		$default_questions = Question::DefaultQuestion(Input::get());
		$default_question = reset($default_questions);

		$data = array(
			"survey" => Survey::first(),
			"filters" => Code::getFilter(),
			"cycles" => Cycle::select('id','name')->get(),
			"question_categories" => QuestionCategory::select('id','name')->get(),
			"question_lists" => Question::select('id','question')->get(),
			"default_question" => $default_question,
			"question" => $default_questions,
			"regions" => Region::RegionColor(),
		);

    if(Request::ajax()){
      return $data;
    }
    else{
			return View::make('home.index', $data);
    }
	}

	public function filterSelect()
	{
		if(Request::ajax()){
			switch (Input::get('SelectedFilter')) {
				case 'area':
					$question_categories_query = Region::QuestionCategoryFilterRegion(Input::get());
					$split_data = Region::SplitQuestionsCategory($question_categories_query);

					$filter_category = (string)View::make('home.filter_category',$split_data)->render();
					$filter_question = (string)View::make('home.filter_question',$split_data)->render();

					$split_data = $filter_category.";".$filter_question;
					break;

				case 'survey':
					$default_questions = Input::get('region') != "null" ? Question::LoadQuestion(Input::get()) : Question::DefaultQuestion(Input::get());;
					$default_question = reset($default_questions);

					$load_filter = array();
					$load_filter = array(
						"survey" => Survey::first(),
						"default_question" => $default_question,
						"question" => $default_questions
					);

					$return = count($default_questions) > 0 ? $load_filter : 0;

					return $return;
					break;

				default:
					# code...
					break;
			}
		}

		return $split_data;
	}
}