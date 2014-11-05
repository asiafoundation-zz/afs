<?php

class CategoryController extends AvelcaController {

	public function __construct(\Category $Model)
	{
		parent::__construct($Model);
	}
	
	public function getIndex(){
		$category = Category::paginate(5);

		return View::make('admin.filter.index')->with('categories', $category);
	}
}