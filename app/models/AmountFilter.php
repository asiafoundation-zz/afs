<?php
class AmountFilter extends Eloquent {

	/* Soft Delete */
	use SoftDeletingTrait;

	/* Eloquent */
	public $table = "amount_filters";
	public $timestamps = true;

	protected $fillable = array(
		'answer_id',
		'category_item_id',
		'region_id',
		'sample_type',
		'amount'
	);

	protected $guarded = array('id');

	public static function checkData($participant_id){
		$amount_participants = Participant::select(
							DB::raw(
									'filter_participants.category_item_id as category_item_id,
									question_participants.answer_id as answer_id,
									question_participants.region_id as region_id,
									question_participants.sample_type'
								)
							)
							->join('filter_participants', 'filter_participants.participant_id', '=', 'participants.id')
							->join('question_participants', 'question_participants.participant_id', '=', 'participants.id')
							->where('participants.id', '=', $participant_id)
							->get();

		foreach($amount_participants as $list){
			$amount = AmountFilter::where('answer_id', '=', $list->answer_id)
			->where('region_id','=', $list->region_id)
			->where('category_item_id', '=', $list->category_item_id)
			->where('sample_type', '=', $list->sample_type)
			->first();

			if(!isset($amount))
			{
				$insert_amount = AmountFilter::create(array(
						'answer_id' => $list->answer_id,
						'region_id' => $list->region_id,
						'sample_type' => $list->sample_type,
						'category_item_id' => (int)$list->category_item_id,
						'amount' => 1
					)
				);
			}
			else
			{
				$amount->amount = $amount->amount+1;
				$amount->save();
			}

		}

	}
}