<?php namespace API\v1;

class RegionController extends \RestController {

	public function __construct(\Region $Model)
	{
		parent::__construct($Model);
	}

}