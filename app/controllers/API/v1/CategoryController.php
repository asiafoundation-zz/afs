<?php namespace API\v1;

class CategoryController extends \RestController {

	public function __construct(\Category $Model)
	{
		parent::__construct($Model);
	}

}