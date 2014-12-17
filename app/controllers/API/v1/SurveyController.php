<?php namespace API\v1;

class SurveyController extends \RestController {

	public function __construct(\Survey $Model)
	{
		parent::__construct($Model);
	}

}