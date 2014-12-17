<?php

class DashboardController extends BaseController {

	public function getIndex()
	{
		$user = Sentry::getUser();
		$groupName = Str::camel($user->groups()->first()->name);

		if ($user->hasAccess('dashboard'))
		{
			return $this->$groupName();
		}
	}

	protected function administrator()
	{
		return View::make('dashboard.administrator');
	}

	
	protected function owner()
	{
		return View::make("dashboard.owner");
	}
	

	protected function admin()
	{
		return View::make("dashboard.admin");
	}
			

}