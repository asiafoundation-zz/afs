<?php namespace API\v1;

class CategoryItemController extends \RestController {

	public function __construct(\CategoryItem $Model)
	{
		parent::__construct($Model);
	}

}