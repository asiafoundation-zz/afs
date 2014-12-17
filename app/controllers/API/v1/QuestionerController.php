<?php namespace API\v1;

class QuestionerController extends \RestController {

	public function __construct(\Questioner $Model)
	{
		parent::__construct($Model);
	}

}