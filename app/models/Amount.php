<?php
class Amount extends Eloquent {

	/* Soft Delete */
	use SoftDeletingTrait;

	/* Eloquent */
	public $table = "amounts";
	public $timestamps = true;

	protected $fillable = array(
		'answer_id',
		'region_id',
		'sample_type',
		'amount',
		'survey_id',
		'category_item_id'
	);

	protected $guarded = array('id');

	public static function checkData($answer_id, $region_id, $sample_type){
		if($sample_type == 0)
		{
			$amount = Amount::where('answer_id', '=', $answer_id)
				->where('region_id','=', $region_id)
				->where('sample_type', '=', 0)
				->first();
			
			if(!isset($amount))
			{
				$insert_amount = Amount::create(array(
						'answer_id' => $answer_id,
						'region_id' => $region_id,
						'sample_type' => $sample_type,
						'amount' => 1
					)
				);
			}
			else
			{
				$amount->amount = $amount->amount+1;
				$amount->save();	
			}
			
			return $amount;
		}
	}
}