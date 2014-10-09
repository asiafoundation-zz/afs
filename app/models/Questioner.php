<?php
class Questioner extends Eloquent {

	/* Soft Delete */
	protected $softDelete = true;

	/* Eloquent */
	public $table = "questioners";
	public $timestamps = true;

	/* Disabled Basic Actions */
	public static $disabledActions = array();

	/* Route */
	public $route = 'questioner';

	/* Mass Assignment */
	protected $fillable = array(
		'amount',
		'is_default',
		'answer_id',
		'category_item_id',
		'region_id'
		);
	protected $guarded = array('id');

	/* Rules */
	public static $rules = array(
		'amount' => 'required|numeric',
		'is_default' => 'required',
		'answer_id' => 'required|numeric',
		'category_item_id' => 'required|numeric',
		'region_id' => 'required|numeric'
		);

	/* Database Structure */
	public static function structure()
	{
		$fields = array(
			'amount' => array(
				'type' => 'number',
				'onIndex' => true
				),
			'is_default' => array(
					'type' => 'text',
					'onIndex' => true
				),
			'answer_id' => array(
					'type' => 'number',
					'onIndex' => true
				),
			'category_item_id' => array(
					'type' => 'number',
					'onIndex' => true
				),
			'region_id' => array(
					'type' => 'number',
					'onIndex' => true
				)
		);

		return compact('fields');
	}


}