<?php
class Code extends Eloquent {

	/* Soft Delete 
	use SoftDeletingTrait;
	protected $dates = ['deleted_at'];
	*/
	/* Eloquent */
	public $table = "codes";
	public $timestamps = true;

	/* Disabled Basic Actions */
	public static $disabledActions = array();

	/* Route */
	public $route = 'code';

	/* Mass Assignment */
	protected $fillable = array(
		'code',
		'master_code_id'
		);
	protected $guarded = array('id');

	/* Rules */
	public static $rules = array(
		'code' => 'required',
		'master_code_id' => 'required'
		);

	/* Database Structure */
	public static function structure()
	{
		$fields = array(
			'code' => array(
				'type' => 'text',
				'onIndex' => true
			),
			'master_code_id' => array(
				'type' => 'number',
				'onIndex' => true
			),
		);

		return compact('fields');
	}

	public static function getFilter()
	{
		$filter_queries = self::select(
				'categories.name',
				'category_items.name as category_item_name')
			->join('master_codes','master_codes.id','=','codes.master_code_id')
			->join('categories','codes.id','=','categories.code_id')
			->join('category_items','categories.id','=','category_items.category_id')
			->where('codes.code', '!=', 'REGION')
			->where('master_codes.attribute_code', '=', 0)
			->get();

		$filters = array();
		if (!$filter_queries->isEmpty()) {
			$i=0;
			foreach ($filter_queries as $key_filter_queries => $filter_query) {
				$filters[$filter_query['name']][$i] = $filter_query['category_item_name'];
				$i++;
			}
		}

		return $filters;
	}
}