<?php
class CategoryItem extends Eloquent {

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
		'category_id'
		);
	protected $guarded = array('id');

	/* Rules */
	public static $rules = array(
		'name' => 'required',
		'category_id' => 'required'
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
		)
			);

		return compact('fields');
	}


}