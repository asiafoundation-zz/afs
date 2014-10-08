<?php

class SurveyController extends AvelcaController {

	public function __construct(\Survey $Model)
	{
		parent::__construct($Model);
	}
	
	public function getIndex(){

		return View::make('admin.survey.index');
	}

	public function getCycle(){

		return view::make('admin.survey.cycle');
	}

	public function getUpload(){

		return View::make('admin.survey.upload');
	}
	
	
}