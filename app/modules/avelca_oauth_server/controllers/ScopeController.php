<?php namespace App\Modules\Avelca_Oauth_Server\Controllers;

use App\Modules\Avelca_Oauth_Server\Models\OauthScope;

use Sentry;
use User;
use View;
use Validator;
use Input;
use Redirect;
use URL;
use Mail;
use Setting;

class ScopeController extends \BaseController {

	public $restful = true;
	
	/* Index */

	public function getIndex()
	{
		$scopes = OauthScope::all();

		$data = array(
			'scopes' => $scopes
			);

		return View::make('avelca_oauth_server::scope.index', $data);
		
	}

	/* Edit */
	
	public function postEdit()
	{
		
		$rules = array(
			'scope' => 'required|max:255',
			'name' => 'required|max:255',
			'description' => 'required|max:255'
			);

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails())
		{
			return Redirect::to('admin/api/scope')->with('messages', $validator->messages());
		}
		else
		{

			$scope = OauthScope::find(Input::get('id'));

			$scope->scope = Input::get('scope');
			$scope->name = Input::get('name');
			$scope->description = Input::get('description');

			if ($scope->save())
			{
				return Redirect::to('admin/api/scope')->with('status', 'Success.');
			}
			else
			{
				return Redirect::to('admin/api/scope')->with('status', 'Failed.');
			}
		}
		
	}

	/* Create */

	public function postCreate()
	{
		
		$rules = array(
			'scope' => 'required|max:255',
			'name' => 'required|max:255',
			'description' => 'required|max:255'
		);

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails())
		{
			return Redirect::to('admin/api/scope')->with('messages', $validator->messages());
		}
		else
		{
			$scope = new OauthScope();
			$scope->scope = strtolower(Input::get('scope'));
			$scope->name = ucwords(Input::get('name'));
			$scope->description = ucfirst(Input::get('description'));

			if($scope->save())
			{
				return Redirect::to('admin/api/scope')->with('status', 'Successfully created client.');
			}
			else
			{
				return Redirect::to('admin/api/scope')->with('status_error', 'Failed to create client');
			}

		}
		
	}

	/* Delete */

	public function postDelete()
	{

		$scope = OauthScope::find(Input::get('id'));
		$scope->delete();

		$status = 'Successfully deleted.';

		return Redirect::to('admin/api/scope')->with('status', $status);
		
	}

}