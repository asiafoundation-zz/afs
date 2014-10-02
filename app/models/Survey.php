<?php
class Survey extends Eloquent {

	/* Soft Delete */
	use SoftDeletingTrait;
	protected $dates = ['deleted_at'];

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
'endline_file'
		);
	protected $guarded = array('id');

	/* Rules */
	public static $rules = array(
		'name' => 'required',
'geojson_file' => 'required',
'baseline_file' => 'required',
'endline_file' => 'required'
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
		)
			);

		return compact('fields');
	}


}