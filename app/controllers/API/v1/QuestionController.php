<?php namespace API\v1;

class QuestionController extends \RestController {

	public function __construct(\Question $Model)
	{
		parent::__construct($Model);
	}

}