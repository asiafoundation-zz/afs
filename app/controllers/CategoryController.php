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
		return Redirect::to('/admin/filter/'.$request['survey_id']);
	}

	public function getFilterorder(){
		$status = 1;

		// Is categories ordered
		$is_categories_ordered = CategoryItem::where('category_id','=',Input::get('category_id'))->first();

		$category = CategoryItem::where('category_id','=',Input::get('category_id'));
		if ($is_categories_ordered->order != 0) {
			$categories = $category->orderBy('order', 'ASC')->get();
		}else{
			$categories = $category->get();
		}

		$view = View::make('admin.filter.order')->with('categories', $categories);
		$view =(string) $view;

		return $view;
	}

	public function postFilterorder(){
		$status = 1;
		$requests = Input::get();

		foreach ($requests['order'] as $key_requests => $request) {
			DB::table('category_items')->where('id', $key_requests)->update(array('order' => $request));
		}

		Session::flash('alert-class', 'alert-success'); 
		Session::flash('message', 'Save Succeed');
		return Redirect::to('/admin/filter/'.$requests['survey_id']);
	}
}