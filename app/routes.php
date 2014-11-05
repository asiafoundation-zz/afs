<?php
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/


/*
|--------------------------------------------------------------------------
| Backend
|--------------------------------------------------------------------------
*/

Route::group(array('before' => 'backend_theme|auth.sentry|password-expiry'), function()
{
	Route::group(array('before' => 'check_permission'), function()
	{
		Route::get('dashboard', 'DashboardController@getIndex');
		
		Route::group(array('prefix' => 'admin'), function()
		{
			AvelcaController::autoRoutes();
			Route::get('survey', 'SurveyController@getIndex');
			Route::get('survey/cycle', 'SurveyController@getCycle');
			Route::post('survey/cycle', 'SurveyController@postCycle');
			Route::get('survey/category/{id}', 'SurveyController@getCategory');
			Route::post('survey/import', 'SurveyController@postImport');
			Route::post('survey/upload', 'SurveyController@postEndline');
			Route::post('survey/region', 'SurveyController@postRegion');
			Route::post('survey/managesurvey', 'SurveyController@getManagesurvey');
			Route::get('survey/reupload', 'SurveyController@reuploadSurvey');			
		});
	});
	
	Route::get('/survey/reupload', 'SurveyController@reupload');
	Route::post('/admin/defaultquestion', 'SurveyController@postDefaultQuestion');
	Route::get('/admin/filter', 'CategoryController@getIndex');

	Route::get('/admin/questioncategory', function(){
		$question_category = QuestionCategory::all();
		return Response::json($question_category);
	});

	Route::get('/admin/question', function(){
		$question = Question::where('question_category_id', '=', Input::get('id_category'))->get();
		return Response::json($question);
	});

});






/*
|--------------------------------------------------------------------------
| Frontend
|--------------------------------------------------------------------------
*/

Route::group(array('prefix' => LaravelLocalization::setLocale(), 'before' => 'frontend_theme'), function()
{
	Route::get('/', 'HomeController@getIndex');
	Route::get('home', 'HomeController@getIndex');
	Route::get('filter-select', 'HomeController@filterSelect');
});

Route::post('cross', function(){
		
		$question_headers = Answer::select(DB::raw('questions.question as question, questions.id as question_id, answers.id as id, answers.answer as answer'))
							->join('questions', 'questions.id', '=', 'answers.question_id')
							->where('questions.id', '=', 1)
							->get();

		$query = "answers.answer as answer,";
		$counter = 0;
		$header_name = array();
		foreach($question_headers as $header){
			$query .= "(
							count(answers.answer) +
							(select count(answers.answer)
								from answers
								join questions
									on questions.id = answers.question_id
								where questions.id = ". $header->question_id ." 
								and answers.id = ". $header->id .")
						) as 'result$counter',";
			$header_name[] = "result$counter";

			$counter ++;

		}

		$query = rtrim($query, ',');

		$question_rows = Answer::select(
							DB::raw($query)
						)
						->join('questions', 'questions.id', '=', 'answers.question_id')
						->where('questions.id', '=', 4)
						->groupBy('answer')
						->get()->toArray();
		// echo "<pre>";
		// print_r($question_rows);		
		// echo "</pre>";
		// exit;
		return array('question_rows' => $question_rows, 'question_headers' => $question_headers, 'header_name' => $header_name);
	});
