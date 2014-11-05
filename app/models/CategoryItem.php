<?php
class CategoryItem extends Eloquent {

/*
 * Type: 
 * 0 = question
 * 1 = filter category
 * 2 = region/area/province
 * 3 = wave
 */

	/* Soft Delete */
	use SoftDeletingTrait;
	protected $dates = ['deleted_at'];

	/* Eloquent */
	public $table = "category_items";
	public $timestamps = true;

	public static $formParent = "category";

	public function category()
	{
		return $this->belongsTo('Category');
	}
				
	/* Disabled Basic Actions */
	public static $disabledActions = array();

	/* Route */
	public $route = 'category-item';

	/* Mass Assignment */
	protected $fillable = array(
		'name',
		'category_id',
		'type'
		);
	protected $guarded = array('id');

	/* Rules */
	public static $rules = array(
		'name' => 'required',
		'category_id' => 'required',
		'type' => 'required'
		);

	/* Database Structure */
	public static function structure()
	{
		$fields = array(
			'name' => array(
			'type' => 'text',
			'onIndex' => true
		),
		'category_id' => array(
			'type' => 'number',
			'onIndex' => true
		),
		'type' => array(
			'type' => 'number',
			'onIndex' => true
		)
			);

		return compact('fields');
	}
	public static function checkData($data,$category_id)
	{
		$category_item = CategoryItem::where('name', '=', $data)->first();
		if(!isset($category_item))
		{
			$category_item = CategoryItem::create(array('name' => $data, 'category_id' => $category_id));
		}	
		return $category_item;
	}
}