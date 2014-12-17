<?php namespace API\v1;

class ColorController extends \RestController {

	public function __construct(\Color $Model)
	{
		parent::__construct($Model);
	}

}