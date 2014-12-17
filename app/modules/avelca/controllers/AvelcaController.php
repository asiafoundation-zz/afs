<?php namespace App\Modules\Avelca\Controllers;

use View;
use Validator;
use Redirect;
use Sentry;
use Input;
use DB;
use Permission;
use Form;
use URL;
use File;
use Request;
use PDF;
use Excel;

class AvelcaController extends \BaseController
{
	/*
	|--------------------------------------------------------------------------
	| Variables
	|--------------------------------------------------------------------------
	|
	*/

	protected $Model;

	protected $bladeLayout = 'layouts/default';

	protected $structure = array();

	protected $viewDir = 'avelca::';

	protected $title = '';

	protected $routeName = '';

	protected $modalDialog = '';

	protected $defaultStructure = array(
		'type' 		=> 'text'
		,'label' 		=> null
		,'onIndex' 		=> false
		,'fillable' 	=> true
		,'editable' 	=> true
		,'attributes' 	=> array()
		,'values' 		=> array()
		);

	protected $trigger = '';

	protected $triggerFields = array();

	protected $formulaResult = '';

	protected $operator = '*';

	protected $operands = array();

	protected $rules = array();

	protected $recordsPerPage = 10;

	/*
	|--------------------------------------------------------------------------
	| Views
	|--------------------------------------------------------------------------
	|
	*/

	public function __construct($Model)
	{
		$this->setModel($Model);
		$this->structure = $Model->structure();
		$this->routeName = $this->Model->route;
	}

	/* Index */
	public function getIndex()
	{
		$recordsPerPage = $this->recordsPerPage();
		$c = get_class($this->Model);

		$records = $this->retrieveRecords();
		$records = (method_exists($c, 'getData') ? $c::getData($recordsPerPage) : $records);
		
		if(Request::ajax() && Input::get('view') == 'paginate'){
			$no = 1;
			$page = Input::get('page');

			if(! empty($page))
			{
				$no = ($page - 1) * $recordsPerPage + 1;
			}

			$customView = 'admin.'.$this->routeName.'.rowtable';
			if( ! View::exists($customView))
			{
				$customView = 'avelca::rowTable';
			}

			$data = array(
				'records'=> $records
				,'indexFields'    => $this->getIndexFields()
				,'routeName' => $this->routeName
				,'actionButtons' => $this->getActionButtons()
				,'disabledActions' => $this->getDisabledActions()
				,'numeric_types' => $this->getNumericTypes()
				,'option_types' => $this->getOptionTypes()
				,'start_no' => $no
				);

			return View::make($customView, $data);
		}

		$data = array(
			'title'       => $this->title
			,'Model'      => $this->Model
			,'routeName'    => $this->routeName
			,'bladeLayout'    => $this->bladeLayout
			,'indexFields'    => $this->getIndexFields()
			,'records'      => $records
			,'mainButtons' => $this->getMainButtons()
			,'actionButtons' => $this->getActionButtons()
			,'disabledActions' => $this->getDisabledActions()
			,'trigger' => $this->getTrigger()
			,'triggerFields' => $this->getTriggerFields()
			,'enctype' => $this->getEnctype()
			,'name' => $this->modelName($this->Model)
			,'numeric_types' => $this->getNumericTypes()
			,'option_types' => $this->getOptionTypes()
			,'modalDialog' => $this->getModalDialog()
			,'rules' => $this->getRules()
			,'start_no' => 1
			);

		return view::make($this->viewDir .'index', $data);
	}

	/* Create */
	public function getCreate()
	{
		$customView = 'admin.'.$this->routeName.'.create';
		$data = array(
			'routeName' => $this->routeName
			,'formItem' => $this->formItem()
			,'formParent' => $this->formParent()
			,'enctype' => $this->getEnctype()
			,'createFields'   => $this->getCreateFields()
			,'rules' => $this->getRules()
			,'trigger' => $this->getTrigger()
			,'triggerFields' => $this->getTriggerFields()			
			,'formulaResult' => $this->getFormulaResult()
			,'operands' => $this->getOperands()
			,'operator' => $this->getOperator()
			,'numeric_types' => $this->getNumericTypes()
			,'option_types' => $this->getOptionTypes()
			);
		if(View::exists($customView))
		{
			return View::make($customView, $data);
		}

		return View::make($this->viewDir . 'create', $data);
	}

	public function postCreate()
	{
		$formItem = $this->formItem();
		$formParent = $this->formParent();

		$input = Input::all();

		$validation = \Validator::make($input, $this->getRules());
		
		if($validation->passes())
		{
			DB::beginTransaction();

			$record = $this->Model->create($input);

			/* Khusus Master Detail */
			if( ! empty($formItem) )
			{
				$Model = '\\'.str_singular(studly_case($formItem));

				foreach ($Model::$rules as $field => $rules) {
					$fields1[$field] = $field;
				}

				foreach ($input as $field => $value) {
					$fields2[$field] = $field;
				}

				$fields = array_intersect($fields1, $fields2);

				$parent = str_singular($Model::$formParent).'_id';

				for($y = 0; $y < count($input[$field]); $y++) {
					if( empty($input[$field][$y]) )
					{
						DB::rollback();
						return \Redirect::to(URL::previous())->with('status_error', 'Please fill all fields.');
					}
					$x = new $Model;
					$x->$parent = $record->id;

					foreach ($fields as $field) {
						$x->$field = $input[$field][$y];
					}
					$x->save();

				}
			}
			/* End Khusus Master Detail */

			DB::commit();

			return \Redirect::to(URL::previous())->with('status', $this->modelName($this->Model).' successfully created.');
		}

		return \Redirect::to(URL::previous())->withErrors($validation)->withInput();
	}

	/* Edit */
	public function getEdit($id)
	{
		$customView = 'admin.'.$this->routeName.'.edit';

		$data = array(
			'Model'            => $this->Model
			,'routeName'        => $this->routeName
			,'indexFields'      => $this->getIndexFields()
			,'fields'           => $this->getViewFields()
			,'editFields'       => $this->getEditFields()
			,'record'          => $this->Model->find($id)
			,'formItem' => $this->formItem()
			,'formParent' => $this->formParent()
			,'trigger' => $this->getTrigger()
			,'triggerFields' => $this->getTriggerFields()
			,'formulaResult' => $this->getFormulaResult()
			,'operands' => $this->getOperands()
			,'operator' => $this->getOperator()
			,'name' => $this->modelName($this->Model)
			,'enctype' => $this->getEnctype()
			,'rules' => $this->getRules()
			,'numeric_types' => $this->getNumericTypes()
			,'option_types' => $this->getOptionTypes()
			);

		if(View::exists($customView))
		{
			return View::make($customView, $data);
		}

		return View::make($this->viewDir . 'edit', $data);
	}

	public function postEdit($id)
	{
		$input = \Input::all();

		$validation = \Validator::make($input, $this->getRules());

		if($validation->passes())
		{
			DB::beginTransaction();

			$record = $this->Model->find($id)->update($input);

			$formItem = $this->formItem();

			/* Master Detail */
			if( ! empty($formItem))
			{
				$Model = str_singular(studly_case($formItem));

				foreach ($Model::$rules as $field => $rules) {
					$fields1[$field] = $field;
				}

				foreach ($input as $field => $value) {
					$fields2[$field] = $field;
				}

				$fields = array_intersect($fields1, $fields2);
				$parent = str_singular($Model::$formParent).'_id';

				$last = count($input[end($fields1)]);
				$total = $Model::where($parent,'=',$id)->get()->count();

				/* Update */

				for($y = 0; $y < $last; $y++) {
					if( empty($input[$field][$y]) )
					{
						DB::rollback();
						return \Redirect::to(URL::previous())->with('status_error', 'Please fill all fields.');
					}


					if( ! empty($input['id'][$y]))
					{
						$record_id = $input['id'][$y];

						$record_item = $Model::find($record_id);
						foreach ($fields as $field) {
							$record_item->$field = $input[$field][$y];
						}
						$record_item->save();
					}
				}

				/* Addition */
				if($last > $total)
				{
					for($z = $total; $z < $last; $z++)
					{
						$record_item = new $Model();
						$record_item->$parent = $id;
						foreach ($fields as $field) {
							$record_item->$field = $input[$field][$z];
						}
						$record_item->save();
					}
				}


				/* Substraction */
				if($last < $total)
				{
					$record_items = $Model::where($parent,'=',$id)->get();
					foreach ($record_items as $record_item)
					{
						$existing_ids[] = $record_item->id;
					}

					foreach ($input['id'] as $submited_id) {
						$submited_ids[] = (int) $submited_id;
					}

					$missing_ids = array_diff($existing_ids, $submited_ids);

					foreach ($missing_ids as $missing_id) {
						$record = $Model::where($parent,'=', $id)
						->where('id','=', $missing_id)
						->delete();
					}
				}				
			}
			/* End Master Detail */

			DB::commit();

			return \Redirect::to(URL::previous())->with('status', $this->modelName($this->Model).' successfully updated.');
		}

		return \Redirect::to(URL::previous())->withErrors($validation);

	}

	/* View */
	public function getView($id)
	{
		$customView = 'admin.'.$this->routeName.'.view';

		$data = array(
			'Model'            => $this->Model
			,'routeName'        => $this->routeName
			,'indexFields'      => $this->getIndexFields()
			,'fields'           => $this->getViewFields()
			,'record'          => $this->Model->find($id)
			,'formItem' => $this->formItem()
			,'formParent' => $this->formParent()
			,'trigger' => $this->getTrigger()
			,'triggerFields' => $this->getTriggerFields()
			,'formulaResult' => $this->getFormulaResult()
			,'operands' => $this->getOperands()
			,'operator' => $this->getOperator()
			,'name' => $this->modelName($this->Model)
			,'enctype' => $this->getEnctype()
			,'numeric_types' => $this->getNumericTypes()
			,'option_types' => $this->getOptionTypes()
			);

		if(View::exists($customView))
		{
			return View::make($customView, $data);
		}

		return View::make($this->viewDir . 'view', $data);
	}

	/* Delete */
	public function getDelete($id)
	{
		$data = array(
			'routeName'  => $this->routeName
			,'record' => $this->Model->find($id)
			,'name' => $this->modelName($this->Model)
			);

		$customView = 'admin.'.$this->routeName.'.delete';

		if(View::exists($customView))
		{
			return View::make($customView, $data);
		}

		return View::make($this->viewDir . 'delete', $data);
	}

	public function postDelete($id)
	{
		$formItem = $this->formItem();

		DB::beginTransaction();

		/* Khusus Master Detail */
		if( ! empty($formItem))
		{
			$this->Model->find($id)->$formItem()->delete();
		}
		/* End Khsusus Master Detail */

		$this->Model->findOrFail($id)->delete();

		DB::commit();

		return \Redirect::to(URL::previous())->with('status', $this->modelName($this->Model).' successfully deleted.');
	}

	/* Print PDF */
	public function getPrintPdf($table = 'index')
	{
		if($table == 'index'){
			$c = get_class($this->Model);
			$data['records'] = $c::all();
			$table = $this->Model;
			$data['indexFields'] = $this->getIndexFields();
		}else{
			$parrent_id = Input::get('parrent_id');
			$parrent = str_replace('-', '_', $this->routeName);
			$parrent = $parrent.'_id';
			$Model = str_singular(studly_case($table));
			$data['records'] = $Model::where($parrent,'=',$parrent_id)->get();
			$data['indexFields'] = $Model::structure()['fields'];
		}

		$data['title'] = $this->title;
		$data['routeName'] = $this->routeName;
		$data['table'] = $table;

		$customView = 'admin.'.$this->routeName.'.delete';
		if( ! View::exists($customView))
		{
			$customView = $this->viewDir .'pdf-table';
		}

		$pdf = PDF::loadView($customView,$data);
		return $pdf->setOrientation('landscape')->stream();
	}

	/* Export Excel */
	public function getExport ($table = 'index')
	{
		if($table == 'index'){
			$c = get_class($this->Model);
			$data['records'] = $c::all();
			$table = $this->Model;
			$data['indexFields'] = $this->getIndexFields();
		}else{
			$parrent_id = Input::get('parrent_id');
			$parrent = str_replace('-', '_', $this->routeName);
			$parrent = $parrent.'_id';
			$Model = str_singular(studly_case($table));
			$data['records'] = $Model::where($parrent,'=',$parrent_id)->get();
			$data['indexFields'] = $Model::structure()['fields'];
		}   

		$data['title'] = $this->title;
		$data['routeName'] = $this->routeName;
		$data['table'] = $table;

		Excel::create($data['routeName']."-excel", function($excel) use($data) {

			$excel->sheet('New sheet', function($sheet) use($data) {
				$sheet->setStyle(array(
					'font' => array(
						'name'      =>  'Calibri',
						'size'      =>  12
						)
					));

				$customView = 'admin.'.$this->routeName.'.excel-table';
				if( ! View::exists($customView))
				{
					$customView = $this->viewDir .'excel-table';
				}
				$sheet->loadView($customView, $data);

			});
		})->export('xls');

	}

	/*
	|--------------------------------------------------------------------------
	| Functions
	|--------------------------------------------------------------------------
	|
	*/

	protected function recordsPerPage()
	{
		$c = get_class($this->Model);
		return isset($c::$recordsPerPage) ? $c::$recordsPerPage : $this->recordsPerPage;
	}

	public static function getSuffixes()
	{
		return $suffixes = array(
			'' => '=',
			'-lk' => 'LIKE',
			'-not-lk' => 'NOT LIKE',
			'-min' => '>=',
			'-max' => '<=',
			'-st' => '<',
			'-gt' => '>',
			'-not' => '!='
			);
	}

	protected function modelName($model)
	{
		$parts = preg_split('/(?=[A-Z])/', get_class($model), -1, PREG_SPLIT_NO_EMPTY);
		$name = '';

		for($i = 0; $i < count($parts); $i++)
		{
			$name .= $parts[$i];

			if($i != (count($parts) - 1) )
			{
				$name .= ' ';
			}
		}
		return $name = trim($name);
	}

	protected function getModalDialog()
	{
		$modalDialog = $this->modalDialog;

		$c = get_class($this->Model);

		if(isset($c::$modalDialog))
		{
			$modalDialog = $c::$modalDialog;

			switch ($modalDialog) {
				case 'small':
				$modalDialog = 'modal-sm';
				break;

				case 'large':
				$modalDialog = 'modal-lg';
				break;
				
				default:
				$modalDialog = '';
				break;
			}
		}

		return $modalDialog;
	}

	protected function getTrigger()
	{
		$c = get_class($this->Model);

		if(isset($c::$formItem))
		{
			$formItem = $c::$formItem;
			$Model = str_singular(studly_case($formItem));

			if(isset($Model::$trigger))
			{
				return $Model::$trigger;				
			}
		}
		
		return $this->trigger;
	}

	protected function getTriggerFields()
	{
		$c = get_class($this->Model);

		if(isset($c::$formItem))
		{
			$formItem = $c::$formItem;
			$Model = str_singular(studly_case($formItem));

			if(isset($Model::$triggerFields))
			{
				return $Model::$triggerFields;				
			}
		}
		
		return $this->triggerFields;
	}

	protected function getFormulaResult()
	{
		$c = get_class($this->Model);

		if(isset($c::$formItem))
		{
			$formItem = $c::$formItem;
			$Model = str_singular(studly_case($formItem));

			$fields = $Model::structure()['fields'];

			foreach ($fields as $field => $structure) {
				if($structure['type'] == 'formula')
				{
					return $field;
				}
			}
		}
		
		return $this->formulaResult;
	}

	protected function getOperator()
	{
		$c = get_class($this->Model);

		if(isset($c::$formItem))
		{
			$formItem = $c::$formItem;
			$Model = str_singular(studly_case($formItem));

			$fields = $Model::structure()['fields'];

			foreach ($fields as $field => $structure) {
				if($structure['type'] == 'formula')
				{
					return $structure['operator'];
				}
			}
		}
		
		return $this->operator;
	}

	protected function getOperands()
	{
		$c = get_class($this->Model);

		if(isset($c::$formItem))
		{
			$formItem = $c::$formItem;
			$Model = str_singular(studly_case($formItem));

			$fields = $Model::structure()['fields'];

			foreach ($fields as $field => $structure) {
				if($structure['type'] == 'formula')
				{
					foreach ($structure['operands'] as $operand) {
						$this->operands[] = $operand;
					}
				}
			}
		}
		
		return $this->operands;
	}

	protected function getRules()
	{
		$c = get_class($this->Model);

		if(isset($c::$formItem))
		{
			$formItem = $c::$formItem;
			$Model = str_singular(studly_case($formItem));
			$new_rules = array_merge($c::$rules, $Model::$rules);
			return $new_rules;
		}

		return isset($c::$rules) ? $c::$rules : array();
	}

	protected function getModel()
	{
		return $this->Model;
	}

	protected function setModel($Model)
	{
		$this->Model = $Model;
	}

	protected function getViewFields()
	{
		return array_filter($this->structure['fields'], function($fields)
		{
			foreach ($fields as $field => $value)
			{
				return true; 
			}
		});
	}

	protected function getIndexFields()
	{
		return array_filter($this->structure['fields'], function($fields)
		{
			foreach ($fields as $field => $value)
			{
				if($this->parseStructure($fields)['onIndex'])
				{
					return true;
				} 
			}
		});
	}

	protected function getCreateFields()
	{
		return array_filter($this->structure['fields'], function($fields)
		{
			foreach ($fields as $field => $value)
			{
				if($this->parseStructure($fields)['fillable'])
				{
					return $this->parseStructure($fields)['fillable'];
				} 
			}
		});
	}

	protected function getEditFields()
	{
		return array_filter($this->structure['fields'], function($fields)
		{
			foreach ($fields as $field => $value)
			{
				if($this->parseStructure($fields)['editable'])
				{
					return $this->parseStructure($fields)['editable'];
				}
			}
		});
	}

	protected function getEnctype()
	{
		$enctype = 'application/x-www-form-urlencoded';
		foreach ($this->structure['fields'] as $field => $structure)
		{
			if($structure['type'] == 'file')
			{
				$enctype = 'multipart/form-data';
			}
		}
		return $enctype;
	}

	protected function getActionButtons()
	{
		$c = get_class($this->Model);
		return isset($c::$actionButtons) ? $c::$actionButtons : array();
	}

	protected function getMainButtons()
	{
		$c = get_class($this->Model);
		return isset($c::$mainButtons) ? $c::$mainButtons : array();
	}

	protected function getDisabledActions()
	{
		$c = get_class($this->Model);
		return isset($c::$disabledActions) ? $c::$disabledActions : array();
	}

	protected function parseStructure($fields)
	{
		return array_merge($this->defaultStructure, $fields);
	}

	protected function getNumericTypes()
	{
		return $numeric_types = array(
			'number',
			'decimal',
			'bignumber',
			'formula'
			);
	}

	protected function getOptionTypes()
	{
		return $option_types = array(
			'radio',
			'switch',
			'datepicker',
			'timepicker',
			'datetimepicker'
			);
	}

	protected function formItem()
	{
		$c = get_class($this->Model);
		return isset($c::$formItem) ? $c::$formItem : null;
	}

	protected function formParent()
	{
		$c = get_class($this->Model);
		return isset($c::$formParent) ? $c::$formParent : null;
	}

	protected function retrieveRecords()
	{
		$c = get_class($this->Model);
		$records = $this->Model;

		if( ! empty($c::$index_conditions) )
		{
			if ( count($c::$index_conditions) > 0)
			{
				foreach ($c::$index_conditions as $condition => $properties) {

					switch ($condition) {
						case 'where':
						$records = $records->where($properties[0], $properties[1], $properties[2]);
						break;

						case 'where_user':
						$records = $records->where($properties[0], $properties[1], Sentry::getUser()->$properties[2]);
						break;

						case 'where_group':
						$records = $records->where($properties[0], $properties[1], Sentry::getUser()->groups()->first()->$properties[2]);
						break;

						case 'auth_user':
						$records = $records->where($properties, '=', Sentry::getUser()->id);
						break;

						case 'auth_group':
						$records = $records->where($properties, '=', Sentry::getUser()->groups()->first()->id);
						break;

						case 'scopes':
						foreach ($properties as $scope) {
							$records = $records->$scope();
						}
						break;
					}
				}
			}
		}


		/* Filter by URL */
		$indexFields = $c::structure()['fields'];
		foreach ($indexFields as $field => $structure) {
			foreach (self::getSuffixes() as $suffix => $operator) {
				if (Input::has($field.$suffix)) {
					$inputValue = Input::get($field.$suffix);
					if( in_array($suffix, array('-lk','-not-lk')) )
					{
						$inputValue = urldecode($inputValue);
						$keywords = explode(" ", $inputValue);

						foreach ($keywords as $keyword) {
							$records = $records->where($field, $operator, '%'.$keyword.'%');
						}
					}
					else
					{
						if($inputValue != '0')
						{
							$records = $records->where($field, $operator, $inputValue);
						}
					}
				}
			}
		}

		if(Input::has('orderBy'))
		{
			$input = Input::get('orderBy');
			$input = explode(',', $input);
			$records = $records->orderBy($input[0], $input[1]);
		}
		else
		{
			if( ! empty($c::$orderBy))
			{
				$orders = $c::$orderBy;

				foreach ($orders as $order => $value) {
					$records = $records->orderBy($order, $value);
				}
			}
			else
			{
				$records = $records->orderBy('id','desc');
			}
		}

		if(Input::has('take'))
		{
			$records = $records->take(Input::get('take'));
		}

		if(Input::has('skip'))
		{
			$records = $records->skip(Input::get('skip'));
		}

		if($c::count() > 0)
		{
			$records = $records->paginate($this->recordsPerPage());

		}
		else
		{ 
			$records = $records->get();
		}

		return $records;
	}

	/*
	|--------------------------------------------------------------------------
	| HTML Form
	|--------------------------------------------------------------------------
	|
	*/

	public static function tableHeader($field, $structure)
	{
		$label = empty($structure['label']) ? $field :  $structure['label'];
		$label = substr($label, -3) == '_id' ? substr($label, 0, strlen($label) - 3) : $label;
		return ucwords(str_replace('_',' ',$label));
	}

	public static function viewIndexContent($record, $structure, $field)
	{
		if($structure['type'] == 'select')
		{
			if( ! empty($structure['table']) )
			{
				$id = $record->id;

				if(substr($field, -3) == '_id')
				{
					$id = $record->$field;
				}

				$Model = ucfirst(str_singular($structure['table']));

				$identifier = self::getIdentifier($structure['table'], $structure);

				$Model = str_replace('_',' ',str_singular($structure['table']));
				$parts = preg_split('/(?=[A-Z])/', $Model, -1, PREG_SPLIT_NO_EMPTY);
				$name = '';

				for($i = 0; $i < count($parts); $i++)
				{
					$name .= $parts[$i];

					if($i != (count($parts) - 1) )
					{
						$name .= ' ';
					}
				}
				$Model = str_replace(' ','',ucwords($name));

				return $Model::find($id)->$identifier;
			}
		}

		if($structure['type'] == 'radio')
		{
			if( ! empty($structure['values']) )
			{
				return $structure['values'][$record->$field];
			}
		}

		if($structure['type'] == 'switch')
		{
			if($record->$field == '1')
			{
				return '<span class="label label-success">Yes</span>';
			}
			else
			{
				return '<span class="label label-danger">No</span>';
			}
		}

		if($structure['type'] == 'url')
		{
			$attributes = array();
			if( ! empty($structure['newTab']) )
			{
				$attributes = array('target' => '_blank');
			}

			$title = null;
			if( ! empty($structure['title']) )
			{
				$title = $structure['title'];
			}

			$url = $record->$field;

			$initial = substr($url, 0, 5);
			switch ($initial) {
				case 'https':
				case 'http:':
				$url = $record->$field;
				break;
				
				default:
				$url = 'http://'.$record->$field;
				break;
			}

			if( ! empty($structure['ellipsis']) )
			{
				$title = substr($url, 0, $structure['ellipsis']).'...';
			}

			return \HTML::link($url, $title, $attributes);
		}

		if($structure['type'] == 'email')
		{
			return \HTML::link('mailto:'.$record->$field, $record->$field);
		}

		if($structure['type'] == 'number')
		{
			return number_format($record->$field,0,',','.');
		}

		if($structure['type'] == 'decimal')
		{
			return $record->$field;
		}

		if($structure['type'] == 'file')
		{
			$ext = pathinfo($record->$field, PATHINFO_EXTENSION);
			if(in_array(strtolower($ext), array('jpg', 'gif', 'png', 'jpeg')))
			{
				$attributes = array('class' => 'img-responsive');

				if( ! empty($structure['max-width']) || ! empty($structure['max-height']) )
				{
					$css_style = '';
					if( ! empty($structure['max-width']) )
					{
						$css_style .= 'max-width: '.$structure['max-width'].'px; ';
					}
					if( ! empty($structure['max-height']) )
					{
						$css_style .= 'max-height: '.$structure['max-height'].'px; ';
					}
					$css_style .= 'margin: 0px auto;';

					$attributes['style'] = $css_style;
				}

				return \HTML::image($record->$field, null, $attributes);
			}

			$title = substr($record->$field,0,30).'...';
			if( ! empty($structure['ellipsis']) )
			{
				$title = substr($record->$field, 0, $structure['ellipsis']).'...';
			}

			return \HTML::link($record->$field, $title, array('title' => $record->$field));
		}

		if($structure['type'] == 'formula')
		{
			$operator = $structure['operator'];
			$operands = $structure['operands'];

			$value = 1;

			foreach ($operands as $operand) {
				switch ($operator) {
					default:
					$value = $value * $record->$operand;
					break;
				}
			}

			return number_format($value,0,',','.');
		}

		if($structure['type'] == 'textarea')
		{
			$content = $record->$field;
			
			if( ! empty($structure['ellipsis']) )
			{
				$content = substr($content, 0, $structure['ellipsis']).'...';
			}

			return $content;
		}

		return $record->$field;
	}

	public static function label($field, $structure, $rules = array())
	{
		$label = empty($structure['label']) ? $field :  $structure['label'];
		$label = substr($label, -3) == '_id' ? substr($label, 0, strlen($label) - 3) : $label;
		$label = ucwords(str_replace('_',' ',$label));
		
		$attributes = array('class' => 'control-label col-md-3');

		if(array_key_exists($field, $rules))
		{
			$label .= ' *';
		}

		return Form::label($field, $label, $attributes);
	}

	/* Field - Table */

	/* Field - Form */
	public static function field($field, $structure, $rules = array())
	{
		$value = null;
		if( ! isset($structure['value']) )
		{
			if(Input::has($field))
			{
				$value = Input::get($field);
			}

			$oldValue = Input::old($field);
			if( ! empty($oldValue))
			{
				$value = $oldValue;
			}
		}
		else
		{
			$value = $structure['value'];
		}

		$attributes = ! isset($structure['attributes']) ? array() : $structure['attributes'];
		$attributes = array_merge(array('class' => 'form-control'), $attributes);
		$help_block = ! isset($structure['help_block']) ? null :  $structure['help_block'];

		if (array_key_exists($field, $rules))
		{
			$attributes = array_merge($attributes, array('required'));
		}

		$type = $structure['type'];

		$editable = empty($structure['editable']) ? true :  $structure['editable'];

		if($editable === false)
		{
			$structure['attributes']['readonly'] = 'readonly';
		}

		$element = '<div class="col-md-4">';

		$inline = empty($structure['inline']) ? $inline = false :  $inline = true;

		switch($type)
		{
			case 'select':
			$table = empty($structure['table']) ? null : ucwords($structure['table']);

			if( ! empty($table))
			{
				$table_model = str_replace('_',' ',str_singular(ucfirst($structure['table'])));
				$parts = preg_split('/(?=[A-Z])/', $table_model, -1, PREG_SPLIT_NO_EMPTY);
				$name = '';

				for($i = 0; $i < count($parts); $i++)
				{
					$name .= $parts[$i];

					if($i != (count($parts) - 1) )
					{
						$name .= ' ';
					}
				}
				$table_model = str_replace(' ','',ucwords($name));

				$rows = $table_model::orderBy('id');

				/* Scopes */
				if ( ! empty($structure['scopes']) )
				{
					foreach ($structure['scopes'] as $scope) {
						$rows = $rows->$scope();
					}
				}
				else
				{
					/* Conditions */
					if ( ! empty($structure['conditions']) )
					{
						foreach ($structure['conditions'] as $condition => $property) {
							$rows = $rows->where($property[0],$property[1],$property[2]);
						}
					}
				}

				$rows = $rows->get();

				$identifier = self::getIdentifier($table, $structure);

				unset($structure['values']);


				foreach ($rows as $row) {
					$optionDisplay = '';
					if ( ! empty($structure['identifier']))
					{
						$i = 1;
						foreach ($structure['identifier'] as $display) {
							$optionDisplay .= $row->$display;
							if($i < count($structure['identifier'])) {
								$optionDisplay .= ' - ';
								$i++;
							}
						}
					}
					else
					{
						$optionDisplay = $row->$identifier;
					}

					$structure['values'][$row->id] = $optionDisplay;
				}
			}

			$selected = empty($structure['selected']) ? null :  $structure['selected'];

			$structure['values'] = empty($structure['values']) ? array() :  $structure['values'];

			$attributes = array_merge($attributes, array('class' => 'selectpicker'));

			$element .= Form::select($field, $structure['values'], $selected, $attributes);

			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}

			$element .= '</div>';
			return $element;
			break;

			case 'radio':

			unset($attributes['class']);

			if($inline)
			{
				$inline = '-inline';
			}

			$selected = empty($structure['selected']) ? null :  $structure['selected'];

			$radios = array();
			foreach($structure['values'] as $value => $display)
			{
				$is_selected = false;

				if ($value == $selected)
				{
					$is_selected = true;
				}

				$radios[] = '<div class="radio'.$inline.'"><label>'.Form::radio($field, $value, $is_selected) . ' ' . $display.'</label></div>';
			}

			$element .= implode("\n", $radios);

			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}

			$element .= '</div>';
			return $element;
			break;

			case 'checkbox':

			unset($attributes['class']);

			if($inline)
			{
				$inline = '-inline';
			}

			$multiple = empty($structure['multiple']) ? $field = $field :  $field = $field.'[]';

			$selected = empty($structure['selected']) ? null :  $structure['selected'];

			$checkboxes = array();
			foreach($structure['values'] as $value => $display)
			{
				$is_selected = false;

				if ($value == $selected)
				{
					$is_selected = true;
				}

				$checkboxes[] = '<div class="checkbox'.$inline.'"><label>'.Form::checkbox($field, $value, $is_selected) . ' ' . $display.'</label></div>';
			}

			$element .= implode("\n", $checkboxes);

			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}
			
			$element .= '</div>';
			return $element;
			break;

			case 'switch':

			unset($attributes['class']);

			$default = empty($structure['default']) ? false :  $structure['default'];

			if($default)
			{
				$element .= Form::hidden($field, '0');
				$element .= '<div class="checkbox"><label></label>'.Form::checkbox($field, '1', true).'</div>';
			}
			else
			{
				$element .= Form::hidden($field, '1');
				$element .= '<div class="checkbox"><label></label>'.Form::checkbox($field, '0').'</div>';
			}

			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}
			
			$element .= '</div>';

			return $element;
			break;

			case 'textarea':
			$attributes = array_merge($attributes, array('class' => 'form-control'));
			$element = '<div class="col-md-9">';
			if ( ! empty($structure['width']) ) {
				$attributes = array_merge($attributes, array('style' => 'width: '.$structure['width'].'px'));
			}
			if ( ! empty($structure['rows']) ) {
				$attributes = array_merge($attributes, array('rows' => $structure['rows']));
			}
			$element .= Form::textarea($field, $value, $attributes);

			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}
			
			$element .= '</div>';
			return $element;
			break;

			case 'longtext':
			$attributes = array_merge($attributes, array('id' => 'editor'));
			$element = '<div class="col-md-9">';
			$element .= Form::textarea($field, $value, $attributes);

			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}
			
			$element .= '</div>';
			return $element;
			break;

			case 'longtext-simple':
			$attributes = array_merge($attributes, array('id' => 'basic_editor'));
			$element = '<div class="col-md-9">';
			$element .= Form::textarea($field, $value, $attributes);

			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}
			
			$element .= '</div>';
			return $element;
			break;

			case 'file':
			$element = '<div class="col-md-4">';

			if ( ! empty($structure['url']) )
			{
				$attributes['placeholder'] = 'URL';
				$element .= Form::text($field.'_direct_url', $value, $attributes);
				$element .= '</div><div class="col-md-4">';
			}

			$element .= Form::file($field, $value, $attributes);

			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}
			
			$element .= '</div>';
			return $element;
			break;

			case 'datepicker':
			$element = '<div class="col-md-3"><div class="input-group datepicker" data-date-format="YYYY-MM-DD">';
			$element .= Form::text($field, $value, $attributes);
			$element .= '<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>';

			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}
			
			$element .= '</div></div>';
			return $element;
			break;

			case 'timepicker':
			$element = '<div class="col-md-2"><div class="input-group timepicker" data-date-format="H:m">';
			$element .= Form::text($field, $value, $attributes);
			$element .= '<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>';

			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}
			
			$element .= '</div></div>';
			return $element;
			break;

			case 'datetimepicker':
			$element = '<div class="col-md-3"><div class="input-group date datepicker" data-date-format="YYYY-MM-DD hh:mm">';
			$value = date('Y-m-d H:i');
			$element .= Form::text($field, $value, $attributes);
			$element .= '<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>';

			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}
			
			$element .= '</div></div>';
			return $element;
			break;

			case 'number':
			case 'bignumber':
			case 'decimal':
			$element = '<div class="col-md-2">';
			$element .= Form::text($field, $value, $attributes);

			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}
			
			$element .= '</div>';
			return $element;
			break;

			case 'colorpicker':
			$attributes = array_merge($attributes, array('id' => 'colorpicker'));
			$element = '<div class="col-md-2">';
			$element .= Form::text($field, $value, $attributes);

			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}
			
			$element .= '</div>';
			return $element;
			break;

			case 'section':
			$element = '<div class="col-md-9">';
			$element .= '<hr><br>';
			$element .= '</div>';
			return $element;
			break;

			case 'formula':
			$attributes = array_merge($attributes, array('disabled'));
			$element .= Form::text($field, $value, $attributes);

			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}
			
			$element .= '</div>';
			return $element;
			break;

			default:
			$element .= Form::text($field, $value, $attributes);

			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}
			
			$element .= '</div>';
			return $element;
			break;
		}		
	}

	/* Field - Form */
	public static function fieldFormItem($field, $structure, $multi = false, $record = null)
	{
		if( ! is_null($record))
		{
			$value = $record->$field;
		}
		else
		{
			$value = empty($structure['value']) ? null :  $structure['value'];
		}

		$attributes = empty($structure['attributes']) ? array() :  $structure['attributes'];
		$attributes = array_merge($attributes, array('class' => 'form-control'));

		$help_block = empty($structure['help_block']) ? null :  $structure['help_block'];

		$type = $structure['type'];

		$editable = empty($structure['editable']) ? true :  $structure['editable'];

		if($editable === false)
		{
			$structure['attributes']['readonly'] = 'readonly';
		}

		$element = '<div class="col-md-12">';

		$inline = empty($structure['inline']) ? $inline = false :  $inline = true;

		if($multi)
		{
			$field = $field.'[]';
		}

		switch($type)
		{
			case 'select':
			$table = empty($structure['table']) ? null : ucwords($structure['table']);

			if( ! empty($table))
			{
				$table_model = str_singular(ucfirst($structure['table']));

				$rows = $table_model::orderBy('id');

				/* Scopes */
				if ( ! empty($structure['scopes']) )
				{
					foreach ($structure['scopes'] as $scope) {
						$rows = $rows->$scope();
					}
				}
				else
				{
					/* Conditions */
					if ( ! empty($structure['conditions']) )
					{
						foreach ($structure['conditions'] as $condition => $property) {
							$rows = $rows->where($property[0],$property[1],$property[2]);
						}
					}
				}

				$rows = $rows->get();

				$identifier = self::getIdentifier($table, $structure);

				unset($structure['values']);

				foreach ($rows as $row) {
					$optionDisplay = '';
					if ( ! empty($structure['identifier']))
					{
						$i = 1;
						foreach ($structure['identifier'] as $display) {
							$optionDisplay .= $row->$display;
							if($i < count($structure['identifier'])) {
								$optionDisplay .= ' - ';
								$i++;
							}
						}
					}
					else
					{
						$optionDisplay = $row->$identifier;
					}

					$structure['values'][$row->id] = $optionDisplay;
				}
			}

			$structure['values'] = empty($structure['values']) ? array() :  $structure['values'];

			$selected = null;
			if( ! is_null($record))
			{
				$selected = $value;
			}

			$attributes = array_merge($attributes, array('class' => 'selectpicker'));

			$element .= Form::select($field, $structure['values'], $selected, $attributes);
			
			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}
			
			$element .= '</div>';
			return $element;
			break;

			case 'radio':

			unset($attributes['class']);

			if($inline)
			{
				$inline = '-inline';
			}

			$selected = empty($structure['selected']) ? null :  $structure['selected'];

			$radios = array();
			foreach($structure['values'] as $value => $display)
			{
				$is_selected = false;

				if ($value == $selected)
				{
					$is_selected = true;
				}

				$radios[] = '<div class="radio'.$inline.'"><label>'.Form::radio($field, $value, $is_selected) . ' ' . $display.'</label></div>';
			}

			$element .= implode("\n", $radios);

			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}
			
			$element .= '</div>';
			return $element;
			break;

			case 'checkbox':

			unset($attributes['class']);

			if($inline)
			{
				$inline = '-inline';
			}

			$selected = empty($structure['selected']) ? null :  $structure['selected'];

			$checkboxes = array();
			foreach($structure['values'] as $value => $display)
			{

				$is_selected = false;

				if ($value == $selected)
				{
					$is_selected = true;
				}

				$checkboxes[] = '<div class="checkbox'.$inline.'"><label>'.Form::checkbox($field, $value, $is_selected) . ' ' . $display.'</label></div>';
			}

			$element .= implode("\n", $checkboxes);

			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}
			
			$element .= '</div>';
			return $element;
			break;

			case 'textarea':
			$attributes = array_merge($attributes, array('class' => 'form-control'));
			$element = '<div class="col-md-9">';
			if ( ! empty($structure['width']) ) {
				$attributes = array_merge($attributes, array('style' => 'width: '.$structure['width'].'px'));
			}
			if ( ! empty($structure['rows']) ) {
				$attributes = array_merge($attributes, array('rows' => $structure['rows']));
			}
			$element .= Form::textarea($field, $value, $attributes);

			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}
			
			$element .= '</div>';
			return $element;
			break;

			case 'longtext':
			$attributes = array_merge($attributes, array('id' => 'editor'));
			$element = '<div class="col-md-9">';
			$element .= Form::textarea($field, $value, $attributes);

			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}
			
			$element .= '</div>';
			return $element;
			break;

			case 'longtext-simple':
			$attributes = array_merge($attributes, array('id' => 'basic_editor'));
			$element = '<div class="col-md-9">';
			$element .= Form::textarea($field, $value, $attributes);

			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}
			
			$element .= '</div>';
			return $element;
			break;

			case 'file':
			$element = '<div class="col-md-4">';

			if ( ! empty($structure['url']) )
			{
				$attributes['placeholder'] = 'URL';
				$element .= Form::text($field.'_direct_url', $value, $attributes);
				$element .= '</div><div class="col-md-4">';
			}

			$element .= Form::file($field, $value, $attributes);

			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}
			
			$element .= '</div>';
			return $element;
			break;

			case 'datepicker':
			$element = '<div class="col-md-3"><div class="input-group datepicker" data-date-format="YYYY-MM-DD">';
			$value = date('Y-m-d');
			$element .= Form::text($field, $value, $attributes);
			$element .= '<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>';

			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}
			
			$element .= '</div></div>';
			return $element;
			break;

			case 'timepicker':
			$element = '<div class="col-md-2"><div class="input-group timepicker" data-date-format="H:m">';
			$value = date('H:i');
			$element .= Form::text($field, $value, $attributes);
			$element .= '<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>';

			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}
			
			$element .= '</div></div>';
			return $element;
			break;

			case 'datetimepicker':
			$element = '<div class="col-md-3"><div class="input-group date datepicker" data-date-format="YYYY-MM-DD hh:mm">';
			$value = date('Y-m-d H:i');
			$element .= Form::text($field, $value, $attributes);
			$element .= '<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>';

			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}
			
			$element .= '</div></div>';
			return $element;
			break;

			case 'colorpicker':
			$attributes = array_merge($attributes, array('id' => 'colorpicker'));
			$element = '<div class="col-md-2">';
			$element .= Form::text($field, $value, $attributes);

			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}
			
			$element .= '</div>';
			return $element;
			break;

			case 'section':
			$element = '<div class="col-md-9">';
			$element .= '<hr><br>';
			$element .= '</div>';
			return $element;
			break;

			case 'formula':

			$operator = $structure['operator'];
			$operands = $structure['operands'];

			foreach ($operands as $operand) {
				switch ($operator) {
					case '*':
					$value = $value * $operand;
					break;
				}
			}

			$attributes = array_merge($attributes, array('disabled'));

			$element .= Form::text($field, $value, $attributes);

			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}
			
			$element .= '</div>';
			return $element;
			break;

			default:
			$element .= Form::text($field, $value, $attributes);
			
			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}
			
			$element .= '</div>';
			return $element;
			break;
		}		
	}

	/* Field - Filter */
	public static function fieldFilter($field, $structure, $rules = array())
	{
		$value = null;
		if( ! isset($structure['value']) )
		{
			if(Input::has($field))
			{
				$value = Input::get($field);
			}

			$oldValue = Input::old($field);
			if( ! empty($oldValue))
			{
				$value = $oldValue;
			}
		}
		else
		{
			$value = $structure['value'];
		}

		$attributes = ! isset($structure['attributes']) ? array() : $structure['attributes'];
		$attributes = array_merge(array('class' => 'form-control'), $attributes);
		$help_block = ! isset($structure['help_block']) ? null :  $structure['help_block'];

		$type = $structure['type'];

		$editable = empty($structure['editable']) ? true :  $structure['editable'];

		if($editable === false)
		{
			$structure['attributes']['readonly'] = 'readonly';
		}

		$element = '<div class="col-md-4">';

		$inline = empty($structure['inline']) ? $inline = false :  $inline = true;

		switch($type)
		{
			case 'select':
			$table = empty($structure['table']) ? null : ucwords($structure['table']);

			if( ! empty($table))
			{
				$table_model = str_replace('_',' ',str_singular(ucfirst($structure['table'])));
				$parts = preg_split('/(?=[A-Z])/', $table_model, -1, PREG_SPLIT_NO_EMPTY);
				$name = '';

				for($i = 0; $i < count($parts); $i++)
				{
					$name .= $parts[$i];

					if($i != (count($parts) - 1) )
					{
						$name .= ' ';
					}
				}
				$table_model = str_replace(' ','',ucwords($name));

				$rows = $table_model::orderBy('id');

				/* Scopes */
				if ( ! empty($structure['scopes']) )
				{
					foreach ($structure['scopes'] as $scope) {
						$rows = $rows->$scope();
					}
				}
				else
				{
					/* Conditions */
					if ( ! empty($structure['conditions']) )
					{
						foreach ($structure['conditions'] as $condition => $property) {
							$rows = $rows->where($property[0],$property[1],$property[2]);
						}
					}
				}

				$rows = $rows->get();

				$identifier = self::getIdentifier($table, $structure);

				unset($structure['values']);


				foreach ($rows as $row) {
					$optionDisplay = '';
					if ( ! empty($structure['identifier']))
					{
						$i = 1;
						foreach ($structure['identifier'] as $display) {
							$optionDisplay .= $row->$display;
							if($i < count($structure['identifier'])) {
								$optionDisplay .= ' - ';
								$i++;
							}
						}
					}
					else
					{
						$optionDisplay = $row->$identifier;
					}

					$structure['values'][$row->id] = $optionDisplay;
				}
			}

			$selected = empty($structure['selected']) ? null :  $structure['selected'];

			$structure['values'] = empty($structure['values']) ? array() :  $structure['values'];

			$structure['values'][0] = 'Any';
			$selected = 0;

			$attributes = array_merge($attributes, array('class' => 'selectpicker'));

			$element .= Form::select($field, $structure['values'], $selected, $attributes);

			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}

			$element .= '</div>';
			return $element;
			break;

			case 'radio':

			unset($attributes['class']);

			if($inline)
			{
				$inline = '-inline';
			}

			$selected = empty($structure['selected']) ? null :  $structure['selected'];

			$structure['values'] = empty($structure['values']) ? array() :  $structure['values'];

			$radios = array();
			$radios[] = '<div class="radio'.$inline.'"><label>'.Form::radio($field, 0, true) . ' Any</label></div>';
			foreach($structure['values'] as $value => $display)
			{
				$radios[] = '<div class="radio'.$inline.'"><label>'.Form::radio($field, $value, false) . ' ' . $display.'</label></div>';
			}

			$element .= implode("\n", $radios);

			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}

			$element .= '</div>';
			return $element;
			break;

			case 'checkbox':

			unset($attributes['class']);

			if($inline)
			{
				$inline = '-inline';
			}

			$multiple = empty($structure['multiple']) ? $field = $field :  $field = $field.'[]';

			$selected = empty($structure['selected']) ? null :  $structure['selected'];

			$structure['values'] = empty($structure['values']) ? array() :  $structure['values'];

			$checkboxes = array();
			$checkboxes[] = '<div class="checkbox'.$inline.'"><label>'.Form::checkbox($field, $value, true) . ' Any</label></div>';
			foreach($structure['values'] as $value => $display)
			{
				$checkboxes[] = '<div class="checkbox'.$inline.'"><label>'.Form::checkbox($field, $value, false) . ' ' . $display.'</label></div>';
			}

			$element .= implode("\n", $checkboxes);

			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}
			
			$element .= '</div>';
			return $element;
			break;

			case 'switch':

			unset($attributes['class']);

			$default = empty($structure['default']) ? false :  $structure['default'];

			if($default)
			{
				$element .= Form::hidden($field, '0');
				$element .= '<div class="checkbox"><label></label>'.Form::checkbox($field, '1', true).'</div>';
			}
			else
			{
				$element .= Form::hidden($field, '1');
				$element .= '<div class="checkbox"><label></label>'.Form::checkbox($field, '0').'</div>';
			}

			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}
			
			$element .= '</div>';

			return $element;
			break;

			case 'textarea':
			$attributes = array_merge($attributes, array('class' => 'form-control'));
			$element = '<div class="col-md-9">';
			if ( ! empty($structure['width']) ) {
				$attributes = array_merge($attributes, array('style' => 'width: '.$structure['width'].'px'));
			}
			if ( ! empty($structure['rows']) ) {
				$attributes = array_merge($attributes, array('rows' => $structure['rows']));
			}
			$element .= Form::textarea($field, $value, $attributes);

			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}
			
			$element .= '</div>';
			return $element;
			break;

			case 'datepicker':

			$value = null;

			$element = '<div class="col-md-3">';

			$element .= '<div class="input-group datepicker" data-date-format="YYYY-MM-DD">';

			if(Input::has($field.'-min'))
			{
				$value = Input::get($field.'-min');
			}

			$attributes = array_merge($attributes, array('placeholder' => 'Min'));
			$element .= Form::text($field.'-min', $value, $attributes);
			$element .= '<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>';
			$element .= '</div></div>';

			$element .= '<div class="col-md-3">';
			$element .= '<div class="input-group datepicker" data-date-format="YYYY-MM-DD">';
			
			if(Input::has($field.'-max'))
			{
				$value = Input::get($field.'-max');
			}

			$attributes = array_merge($attributes, array('placeholder' => 'Max'));
			$element .= Form::text($field.'-max', $value, $attributes);
			$element .= '<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>';
			$element .= '</div>';

			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}
			
			
			$element .= '</div>';
			return $element;
			break;

			case 'timepicker':
			$element = '<div class="col-md-2"><div class="input-group timepicker" data-date-format="H:m">';
			$element .= Form::text($field, $value, $attributes);
			$element .= '<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>';

			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}
			
			$element .= '</div></div>';
			return $element;
			break;

			case 'datetimepicker':
			$element = '<div class="col-md-3"><div class="input-group date datepicker" data-date-format="YYYY-MM-DD hh:mm">';
			$value = date('Y-m-d H:i');
			$element .= Form::text($field, $value, $attributes);
			$element .= '<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>';

			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}
			
			$element .= '</div></div>';
			return $element;
			break;

			case 'number':
			case 'bignumber':
			case 'decimal':

			$element = '<div class="col-md-2">';
			if(Input::has($field.'-min'))
			{
				$value = Input::get($field.'-min');
			}
			$attributes = array_merge($attributes, array('placeholder' => 'Min'));
			$element .= Form::text($field.'-min', $value, $attributes);
			$element .= '</div>';

			$element .= '<div class="col-md-2">';
			if(Input::has($field.'-max'))
			{
				$value = Input::get($field.'-max');
			}
			$attributes = array_merge($attributes, array('placeholder' => 'Max'));
			$element .= Form::text($field.'-max', $value, $attributes);
			$element .= '</div>';


			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}
			
			return $element;
			break;

			case 'colorpicker':
			$attributes = array_merge($attributes, array('id' => 'colorpicker'));
			$element = '<div class="col-md-2">';
			$element .= Form::text($field, $value, $attributes);

			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}
			
			$element .= '</div>';
			return $element;
			break;

			case 'section':
			$element = '<div class="col-md-9">';
			$element .= '<hr><br>';
			$element .= '</div>';
			return $element;
			break;

			case 'formula':
			$attributes = array_merge($attributes, array('disabled'));
			$element .= Form::text($field, $value, $attributes);

			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}
			
			$element .= '</div>';
			return $element;
			break;

			default:
			if(Input::has($field.'-lk'))
			{
				$value = Input::get($field.'-lk');
			}
			$element .= Form::text($field.'-lk', $value, $attributes);

			if( ! is_null($help_block) )
			{
				$element .= '<span class="help-block">'.$help_block.'</span>';
			}
			
			$element .= '</div>';
			return $element;
			break;
		}		
	}

	protected static function getIdentifier($table, $structure)
	{
		$table = strtolower($table);
		$identifier = 'id';

		if(\Schema::hasColumn($table, 'code'))
		{
			$identifier = 'code';
		}
		if(\Schema::hasColumn($table, 'title'))
		{
			$identifier = 'title';
		}
		if(\Schema::hasColumn($table, 'last_name'))
		{
			$identifier = 'last_name';
		}
		if(\Schema::hasColumn($table, 'first_name'))
		{
			$identifier = 'first_name';
		}
		if(\Schema::hasColumn($table, 'full_name'))
		{
			$identifier = 'full_name';
		}
		if(\Schema::hasColumn($table, 'name'))
		{
			$identifier = 'name';
		}

		return $identifier;
	}

	public static function autoRoutes()
	{
		$files = \File::files(app_path().'/controllers');

		$standard_controllers = array(
			'HomeController',
			'BaseController',
			'DashboardController'
			);

		foreach ($files as $file) {
			$name = explode("/", $file);

			$name = end($name);
			$name = str_replace(".php", "", $name);
			$routeName = str_replace("Controller", '', $name);

			$parts = preg_split('/(?=[A-Z])/', $routeName, -1, PREG_SPLIT_NO_EMPTY);
			$routeName = '';

			for($i = 0; $i < count($parts); $i++)
			{
				$routeName .= $parts[$i];

				if($i != (count($parts) - 1) )
				{
					$routeName .= '-';
				}
			}

			$routeName = strtolower($routeName);

			if( ! in_array($name, $standard_controllers))
			{
				\Route::controller($routeName, $name);
			}
		}
	}

	public static function mainNavigation()
	{
		$files = \File::files(app_path().'/models');
		$user = Sentry::getUser();

		$master = '';
		$transaction = '';

		foreach($files as $file) {

			$name = str_replace('.php','',basename($file));
			$Model = '\\'.$name;

			$parts = preg_split('/(?=[A-Z])/', $name, -1, PREG_SPLIT_NO_EMPTY);
			$name = '';
			$url = '';

			for($i = 0; $i < count($parts); $i++)
			{
				$name .= $parts[$i];
				$url .= $parts[$i];

				if($i != (count($parts) - 1) )
				{
					$name .= ' ';
					$url .= '-';
				}
			}
			$name = trim($name);
			$url = 'admin/'.strtolower(trim($url));

			if(substr($name, -5) != ' Item' ) {
				if( $user->hasAccess(str_replace(' ','-',strtolower($name))) ) {
					if( ! isset($Model::$formItem) )
					{
						$master .= '
						<li>
						<a href="'.URL::to($url).'">'.$name.'</a>
						</li>
						';
					}
					else
					{
						$transaction .= '
						<li>
						<a href="'.URL::to($url).'">'.$name.'</a>
						</li>
						';
					}
				}
			}
		}

		?>

		<li>
			<a href="#"><i class="glyphicon glyphicon-th-large"></i> Master<span class="fa arrow"></span></a>
			<ul class="nav nav-second-level">
				<?php echo $master; ?>
			</ul>
		</li>

		<li>
			<a href="#"><i class="glyphicon glyphicon-list-alt"></i> Transaction<span class="fa arrow"></span></a>
			<ul class="nav nav-second-level">
				<?php echo $transaction; ?>
			</ul>
		</li>

		<?php

	}

	/* End HTML Form */

	/* Avelca Artisan Command */
	public static function avelcaInstall($cmd)
	{
		$logo = "
		█████╗ ██╗   ██╗███████╗██╗      ██████╗ █████╗ 
		██╔══██╗██║   ██║██╔════╝██║     ██╔════╝██╔══██╗
		███████║██║   ██║█████╗  ██║     ██║     ███████║
		██╔══██║╚██╗ ██╔╝██╔══╝  ██║     ██║     ██╔══██║
		██║  ██║ ╚████╔╝ ███████╗███████╗╚██████╗██║  ██║
		╚═╝  ╚═╝  ╚═══╝  ╚══════╝╚══════╝ ╚═════╝╚═╝  ╚═╝
		";

		$cmd->comment($logo);
		$cmd->comment("Welcome to Avelca Application Installer. Press enter to continue.\n");

		$value = $cmd->option('nokey');

		if( empty($value) )
		{
			$cmd->info("1) Generate application secret key.\n");
			\Artisan::call('key:generate');			
		}
		else
		{
			$cmd->info("1) Do not generate application secret key.\n");
		}


		$cmd->info("2) Migrate, seed and publish user configuration file.");
		\Artisan::call('modules:migrate', array('avelca_user'));

		// \Artisan::call('config:publish', array('package' => 'cartalyst/sentry'));

		$cmd->info("3) Seed avelca module permission.");
		\Artisan::call('modules:migrate', array('avelca_module'));

		$cmd->info("4) Migrate and seed avelca setting.");
		\Artisan::call('modules:migrate', array('avelca_setting'));

		$seed = new \App\Modules\Avelca_User\Seeds\DatabaseSeeder;
		$seed->run();

		$seed = new \App\Modules\Avelca_Setting\Seeds\DatabaseSeeder;
		$seed->run();

		$seed = new \App\Modules\Avelca_Module\Seeds\DatabaseSeeder;
		$seed->run();

		$cmd->info("5) Migrate and seed.");
		\Artisan::call('migrate');
		\Artisan::call('db:seed');

		$cmd->comment("Installation ................. [SUCCESS]\n");
		$cmd->info("Continue installation at http://[your_app_url]/install to create administrator user.\n");
	}

	public static function createAdministratorPermissions()
	{
		Group::truncate();

		$permissions = Permission::all();
		$all_permission = array();

		foreach ($permissions as $permission) {
			$all_permission[$permission->name] = 1;
		}

		Sentry::getGroupProvider()->create(array(
			'name'        => 'Administrator',
			'permissions' => $all_permission
			));

		return true;
	}
}