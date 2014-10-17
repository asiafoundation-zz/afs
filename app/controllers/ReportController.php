<?php

class ReportController extends BaseController {

    
	public function getReportC()
	{
		/* Description */
		$name = "Report C";
		$url_name = "report-c";

		/* Records */
		$records = array();

		/* Data for View */
		$data = array(
			"routeName" => "report/".$url_name,
			"title" => "Report ".$name,
			"name" => $name,
			"records" => $records
			);

		return View::make("admin.report.report-category-b.".$url_name, $data);
	}


	public function getReportD()
	{
		/* Description */
		$name = "Report D";
		$url_name = "report-d";

		/* Records */
		$records = array();

		/* Data for View */
		$data = array(
			"routeName" => "report/".$url_name,
			"title" => "Report ".$name,
			"name" => $name,
			"records" => $records
			);

		return View::make("admin.report.report-category-b.".$url_name, $data);
	}



}