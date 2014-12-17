<?php

View::creator('partial.sidemenu', function($view)
{
	$menus = Session::get('menus');

	/* Modify This */
	$data = array(
		'text' => 'Resource',
		'url' => '#',
		'icon' => 'fa fa-magic fa-fw',
		'show' => false
		);
	/* End Modify This */

	if (empty($menus))
	{
		Session::put('menus', array($data));
	}
	else
	{
		Session::push('menus', $data);
	}

	$menus = Session::get('menus');

	$view->with('menus', $menus);
});