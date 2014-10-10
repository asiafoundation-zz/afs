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
		$questions =  DB::table('regions')
			->select(
				'questioners.region_id as region_id',
				'questioners.answer_id as answer_id',
				'regions.name',
				'colors.color as color'
				)
			->join('questioners','questioners.region_id','=','regions.id')
			->join('answers','answers.id','=','questioners.answer_id')
			->join('colors','colors.id','=','answers.color_id')
			->whereRaw('questioners.id = (SELECT MAX(amount) FROM questioners WHERE questioners.region_id = region_id AND questioners.answer_id = answer_id)')
			->get();

		return $questions;
	}
}