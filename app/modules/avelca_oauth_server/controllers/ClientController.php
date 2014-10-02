<?php namespace App\Modules\Avelca_Oauth_Server\Controllers;

use App\Modules\Avelca_Oauth_Server\Models\OauthClient;

use Sentry;
use User;
use View;
use Validator;
use Input;
use Redirect;
use URL;
use Mail;
use Setting;

class ClientController extends \BaseController {

	public $restful = true;
	
	/* Index */

	public function getIndex()
	{
		$clients = OauthClient::all();

		$data = array(
			'clients' => $clients
			);

		return View::make('avelca_oauth_server::client.index', $data);
		
	}

	/* Edit */
	
	public function postEdit()
	{
		
		$rules = array(
			'name' => 'required'

			);

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails())
		{
			return Redirect::to('admin/api/client')->with('messages', $validator->messages());
		}
		else
		{

			$client = OauthClient::find(Input::get('id'));

			$client->name = Input::get('name');

			if ($client->save())
			{
				return Redirect::to('admin/api/client')->with('status', 'Success.');
			}
			else
			{
				return Redirect::to('admin/api/client')->with('status', 'Failed.');
			}
		}
		
	}

	/* Create */

	public function postCreate()
	{
		
		$rules = array(
			'id' => 'required|max:40',
			'secret' => 'required|max:40',
			'name' => 'required|max:255'
			);

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails())
		{
			return Redirect::to('admin/api/client')->with('messages', $validator->messages());
		}
		else
		{
			$client = new OauthClient();
			$client->id = Input::get('id');
			$client->secret = Input::get('secret');
			$client->name = Input::get('name');

			if($client->save())
			{
				return Redirect::to('admin/api/client')->with('status', 'Successfully created client.');
			}
			else
			{
				return Redirect::to('admin/api/client')->with('status_error', 'Failed to create client');
			}

		}
		
	}

	/* Delete */

	public function postDelete()
	{

		$client = OauthClient::find(Input::get('id'));
		$client->delete();

		$status = 'Successfully deleted.';

		return Redirect::to('admin/api/client')->with('status', $status);
		
	}

}