<?php
class MasterCode extends Eloquent {

	/* Soft Delete 
	use SoftDeletingTrait;
	protected $dates = ['deleted_at'];
	*/

	/* Eloquent */
	public $table = "master_codes";
	public $timestamps = true;

	public static $formItem = "codes";

	public function codes()
	{
		return $this->hasMany('Codes');
	}

	/* Disabled Basic Actions */
	public static $disabledActions = array();

	/* Route */
	public $route = 'master_code';

	/* Mass Assignment */
	protected $fillable = array(
		'master_code',
		'attribute_code'
		);
	protected $guarded = array('id');

	/* Rules */
	public static $rules = array(
		'master_code' => 'required'
		'attribute_code' => 'required'
		);

	/* Database Structure */
	public static function structure()
	{
		$fields = array(
			'master_code' => array(
			'type' => 'text',
			'onIndex' => true
			),
			'attribute_code' => array(
			'type' => 'number',
			'onIndex' => true
		);

		return compact('fields');
	}


}