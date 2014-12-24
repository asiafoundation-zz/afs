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
			Route::get('survey/edit/{id}', 'SurveyController@getDefaultquestion');
			Route::get('survey/cycle', 'SurveyController@getCycle');
			Route::post('survey/cycle', 'SurveyController@postCycle');
			Route::get('survey/category/{id}', 'SurveyController@getCategory');
			Route::post('survey/category', 'SurveyController@postCategory');
			Route::post('survey/import', 'SurveyController@postImport');
			// Route::post('survey/upload', 'SurveyController@postEndline');
			Route::post('survey/region', 'SurveyController@postRegion');
			Route::get('/survey/reupload', 'SurveyController@reupload');
			Route::get('survey/managesurvey/{id}', 'SurveyController@getManagesurvey');
			Route::get('survey/defaultquestion/{id}', 'SurveyController@getDefaultquestion');
			Route::post('survey/defaultquestion', 'SurveyController@postDefaultquestion');
		});
	});

	Route::get('survey/cyclelist', 'SurveyController@getCyclelist');	
	Route::get('/survey/reupload', 'SurveyController@reupload');
	Route::get('/admin/filter/{id}', 'CategoryController@getManagefilter');
	Route::post('/admin/filter', 'CategoryController@postManagefilter');
	Route::post('/admin/filterorder', 'CategoryController@postFilterorder');
	Route::get('/admin/filterorder', 'CategoryController@getFilterorder');
	Route::get('/admin/logs/', 'LogController@getIndex');
	Route::get('/survey/singledelete/{id}', 'SurveyController@deleteSurvey');
	Route::get('/admin/cycle/{id}', 'CategoryController@getCycle');
	Route::post('/admin/cycle', 'CategoryController@postCycle');

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
// Route::get('language/{lang}', 
//            array(
//                   'as' => 'language.select', 
//                   'uses' => 'HomeController@getLang'
//                  )
//           );

Route::post('language', 'HomeController@postLang');

Route::get('/', array('as' => 'home', 'uses'=>'HomeController@getIndex'));

Route::group(array('prefix' => LaravelLocalization::setLocale(), 'before' => 'frontend_theme'), function()
{
	Route::get('/', 'HomeController@getIndex');
	Route::get('home', 'HomeController@getIndex');
	Route::get('/filter-select', 'HomeController@filterSelect');
});

Route::post('cross', 'AnswerController@postCross');
Route::post('loadcategory', function(){
	$question = Question::where('question_category_id', '=', Input::get('id_cat'))->get();
	return Response::json($question);
});

Route::post('cycleLang', function(){
	$category = Lang::get('frontend.choose_category');
	$question = Lang::get('frontend.choose_question');
	$cycle_info = Lang::get('frontend.cycle_info');

	return array($category, $question, $cycle_info);
});


