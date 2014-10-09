<?php

class HomeController extends BaseController {

	public function getIndex()
	{
		$data = array();
		$data["filters"] = Code::getFilter();

		return View::make('home.index', $data);
	}
}