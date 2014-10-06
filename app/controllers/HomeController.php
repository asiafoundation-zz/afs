<?php

class HomeController extends BaseController {

	public function getIndex()
	{
		$data = array();
		$data["data"] = "aaa";
		return View::make('home.index', $data);
	}
}