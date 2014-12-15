<?php namespace API\v1;

class AnswerController extends \RestController {

	public function __construct(\Answer $Model)
	{
		parent::__construct($Model);
	}

}