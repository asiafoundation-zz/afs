<?php

class CategoryController extends AvelcaController {

	public function __construct(\Category $Model)
	{
		parent::__construct($Model);
	}
	
	public function getManagefilter($id){
		$categories = Category::where('survey_id','=',$id)->get();

		return View::make('admin.filter.index')
			->with('categories', $categories)
			->with('survey_id', $id);
	}

	public function postManagefilter(){
		$status = 1;
		$request = Input::get();

		$categories = Category::where('id','=',Input::get('category_id'))->first();
		if (isset($request['is_active'])) {
			$categories->is_active = $request['is_active'];
		}
		if (!empty($request['display_name'])) {
			$categories->display_name = $request['display_name'];
		}

		$categories->save();

		Session::flash('alert-class', 'alert-success'); 
		Session::flash('message', 'Save Succeed');
		return $status;
	}
}