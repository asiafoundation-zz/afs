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

	public static function RegionColor()
	{
		$region_queries =  DB::table('regions')
			->select(
				'regions.id as region_id',
				'answers.id as answer_id',
				'regions.name',
				'colors.color as color',
				'questioners.amount'
			)
			->join('questioners','questioners.region_id','=','regions.id')
			->join('answers','answers.id','=','questioners.answer_id')
			->join('colors','colors.id','=','answers.color_id')
			->get();

			/*
			 * Get regions with maximum values votes
			 */
			$regions = array();
			if (count($region_queries)) {
				foreach ($region_queries as $key_region_queries => $region_query) {
					$regions[$region_query->name]["region_id"] = $region_query->region_id;
					$regions[$region_query->name]["name"] = $region_query->name;

					if (empty($regions[$region_query->name]["amount"])) {
						$regions[$region_query->name]["answer_id"] = $region_query->answer_id;
						$regions[$region_query->name]["amount"] = $region_query->amount;
						$regions[$region_query->name]["color"] = $region_query->color;
					}
					if ($region_query->amount > $regions[$region_query->name]["amount"]) {
						$regions[$region_query->name]["answer_id"] = $region_query->answer_id;
						$regions[$region_query->name]["amount"] = $region_query->amount;
						$regions[$region_query->name]["color"] = $region_query->color;
					}
				}
			}

		return $regions;
	}
}