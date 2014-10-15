<?php

class HomeController extends BaseController {

	public function getIndex()
	{
		// Get Default Question
		$default_questions = Question::DefaultQuestion(Input::get());
		$default_question = reset($default_questions);

		// Get catefory and question list
		$question_categories_query = QuestionCategory::QuestionCategoryFilterRegion(Input::get());
		$split_data = QuestionCategory::SplitQuestionsCategory($question_categories_query);

		$data = array(
			"survey" => Survey::first(),
			"filters" => Code::getFilter(),
			"cycles" => Cycle::select('id','name')->get(),
			"question_categories" => $split_data['question_categories'],
			"question_lists" => $split_data['question_lists'],
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
					$question_categories_query = QuestionCategory::QuestionCategoryFilterRegion(Input::get());
					$split_data = QuestionCategory::SplitQuestionsCategory($question_categories_query);

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