<?php
class Code extends Eloquent {

/* Code Types
 * 0 = area/region/province
 * 1 = wave
 * 2 = oversample
 * 3 = filter category
 * 4 = question
 */

	/* Soft Delete */
	protected $softDelete = true;

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
		'master_code_id',
		'type'
		);
	protected $guarded = array('id');

	/* Rules */
	public static $rules = array(
		'code' => 'required',
		'master_code_id' => 'required',
		'type' => 'required'
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
			'type' => array(
				'type' => 'number',
				'onIndex' => true
			),
		);

		return compact('fields');
	}

	public function master_code()
	{
		return $this->belongsTo('MasterCode');
	}

	public static function getFilter($survey_id)
	{
		$filter_queries = self::select(
				'categories.id as category_id',
				'categories.name as category_name',
				'categories.display_name as name',
				'category_items.id as category_item_id',
				'category_items.name as category_item_name',
				'category_items.order as order')
			->join('master_codes','master_codes.id','=','codes.master_code_id')
			->join('categories','codes.id','=','categories.code_id')
			->join('category_items','categories.id','=','category_items.category_id')
			->where('codes.code', '!=', 'REGION')
			->where('master_codes.attribute_code', '=', 0)
			->where('categories.is_active', '=', 1)
			->where('categories.survey_id', '=', $survey_id)
			->get();

		$filters = array();
		if (!$filter_queries->isEmpty()) {
			foreach ($filter_queries as $key_filter_queries => $filter_query) {
				$filters[$filter_query['category_id']]['category_items'][$filter_query['category_item_id']]['order'] = $filter_query['order'];
				$filters[$filter_query['category_id']]['category_items'][$filter_query['category_item_id']]['category_item_id'] = $filter_query['category_item_id'];
				$filters[$filter_query['category_id']]['category_items'][$filter_query['category_item_id']]['category_item_name'] = $filter_query['category_item_name'];
				$filters[$filter_query['category_id']]['category_name'] = !empty($filter_query['name']) ? $filter_query['name'] : $filter_query['category_name'];

				usort($filters[$filter_query['category_id']]['category_items'], function($a, $b) {
					return $a['order'] - $b['order'];
				});
			}
		}
		return $filters;
	}
}