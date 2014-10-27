<?php
class Survey extends Eloquent {

	/* Soft Delete */
	protected $softDelete = true;

	/* Eloquent */
	public $table = "surveys";
	public $timestamps = true;


	/* Disabled Basic Actions */
	public static $disabledActions = array();

	/* Route */
	public $route = 'survey';

	/* Mass Assignment */
	protected $fillable = array(
		'name',
		'geojson_file',
		'baseline_file',
		'endline_file',
		'publish'
		);
	protected $guarded = array('id');

	/* Rules */
	public static $rules = array(
		'name' => 'required',
		'geojson_file' => 'required',
		'baseline_file' => 'required',
		'endline_file' => 'required',
		'publish' => 'required'
		);

	/* Database Structure */
	public static function structure()
	{
		$fields = array(
			'name' => array(
			'type' => 'text',
			'onIndex' => true
			),
			'geojson_file' => array(
						'type' => 'text',
						'onIndex' => true
					),
			'baseline_file' => array(
						'type' => 'text',
						'onIndex' => true
					),
			'endline_file' => array(
						'type' => 'text',
						'onIndex' => true
					),
			'publish' => array(
						'type' => 'number',
						'onIndex' => true
					)
		);

		return compact('fields');
	}
}