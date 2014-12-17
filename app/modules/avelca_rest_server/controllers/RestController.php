<?php namespace App\Modules\Avelca_Rest_Server\Controllers;

use Illuminate\Routing\Controller;

class RestController extends Controller
{
	var $record = '';
	var $rules = array();

	public function __construct($Model)
	{
		$this->record = $Model;
		$this->rules = $this->getRules();
	}

	public function index()
	{
		$result = \ApiHandler::parseMultiple($this->record, array('name'));

		$response = array(
			'collections' => $result->getResult()->toArray(),
			'response_code' => '200'
			);

		$meta_response = array();

		if(\Input::has('_config'))
		{
			$config = explode(',', \Input::get('_config'));

			$config[] = '';

			if( $config[0] == 'meta-total-count' || $config[1] == 'meta-total-count' )
			{
				$meta_response = array_merge($meta_response, array('total_count' => $this->record->count()));
			}

			if( $config[0] == 'meta-filter-count' || $config[1] == 'meta-filter-count' )
			{
				$meta_response = array_merge($meta_response, array('filter_count' => count($result->getResult()) ));
			}
		}

		if( ! empty($meta_response))
		{
			$response = array_merge($response, array('meta_data' => $meta_response));
		}

		return \Response::json($response, 200);
	}

	public function store()
	{
		$validation = \Validator::make(\Input::all(), $this->rules);
		
		if($validation->passes())
		{
			$instance = $this->record;
			$instance = $instance->create(\Input::all());

			$result = \ApiHandler::parseSingle($this->record, $instance->id);

			$response = array(
				'instance' => $result->getResult()->toArray(),
				'response_code' => '201'
				);
		}
		else
		{
			$response = array(
				'message' => 'Form Validation Failed',
				'response_code' => '404'
				);
		}
		
		return \Response::json($response, 201);
	}

	public function show($id)
	{
		if( $this->is_exist($id) )
		{
			$this->record = $this->record->find($id);
			$result = \ApiHandler::parseSingle($this->record, $id);	

			$response = array(
				'instance' => $result->getResult()->toArray(),
				'response_code' => '200'
				);		
		}
		else
		{
			$response = array(
				'response_code' => '404'
				);
		}

		return \Response::json($response, 200);
	}

	public function destroy($id)
	{
		if( $this->is_exist($id) )
		{
			$instance = $this->record->find($id);
			$instance->delete();

			$response = array(
				'response_code' => '200'
				);
		}
		else
		{
			$response = array(
				'response_code' => '404'
				);
		}
		return \Response::json($response, 200);

	}

	public function update($id)
	{
		if( $this->is_exist($id) )
		{
			$instance = $this->record->find($id);
			$instance->update(\Input::all());

			$result = \ApiHandler::parseSingle($this->record, $id);

			$response = array(
				'instance' => $result->getResult()->toArray(),
				'response_code' => '200'
				);
		}
		else
		{
			$response = array(
				'response_code' => '404'
				);
		}
		return \Response::json($response, 200);
	}

	private function is_exist($id)
	{
		$is_exist = $this->record->find($id);

		if( ! is_null($is_exist) )
		{
			return true;	
		}
		else
		{
			return false;
		}
	}

	private function getRules()
	{
		$c = get_class($this->record);

		if(isset($c::$formItem))
		{
			$formItem = $c::$formItem;
			$Model = str_singular(studly_case($formItem));
			$new_rules = array_merge($c::$rules, $Model::$rules);
			return $new_rules;
		}

		return isset($c::$rules) ? $c::$rules : array();
	}
}