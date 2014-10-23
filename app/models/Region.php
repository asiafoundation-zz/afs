<?php
class Region extends Eloquent {

	/* Soft Delete 
	use SoftDeletingTrait;
	protected $dates = ['deleted_at'];
	*/
	/* Eloquent */
	public $table = "regions";
	public $timestamps = true;


	/* Disabled Basic Actions */
	public static $disabledActions = array();

	/* Route */
	public $route = 'region';

	/* Mass Assignment */
	protected $fillable = array(
		'name',
		'code_id'
		);
	protected $guarded = array('id');

	/* Rules */
	public static $rules = array(
		'name' => 'required',
		'code_id' => 'required',
		);

	/* Database Structure */
	public static function structure()
	{
		$fields = array(
			'name' => array(
				'type' => 'text',
				'onIndex' => true
			),
			'code_id' => array(
				'type' => 'number',
				'onIndex' => true
			)
			);

		return compact('fields');
	}
}