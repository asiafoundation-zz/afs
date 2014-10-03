<?php

/* Form Validation */
Widget::register('form-validation', function(){
    return View::make('widgets.form-validation.index');
});

/* Greeting */
Widget::register('greeting', function(){
    return View::make('widgets.greeting.index');
});

/* Add to Any */
Widget::register('addtoany', function(){
    return View::make('widgets.addtoany.index');
});

/* Laravel Version */
Widget::register('laravel_version', function(){
    return View::make('widgets.laravel_version.index');
});

/* Dashboard Table */
/* Example :
 * {{ Widget::dashboardTable('Page', array('title', 'created_at'), array(array('title','=','About Us'))) }}
 */
Widget::register('dashboardTable', function($table, $fields, $conditions = array(), $limit, $order_by = 'id'){

	$Model = str_replace(' ','',ucwords(str_replace('_',' ',$table)));
	$data = $Model::orderBy($order_by, 'desc');

	foreach ($conditions as $condition => $value) {
		$data = $data->where($value[0],$value[2],$value[2]);
	}

	$records = $data->limit($limit)->get();

	$data = array(
		'records' => $records,
		'fields' => $fields
		);

    return View::make('widgets.dashboard_table.index', $data);
});