<?php

class LogController extends AvelcaController {

	public function __construct(\Answer $Model)
	{
		parent::__construct($Model);
	}

	public function getIndex()
	{
		$participant_loads = Participant::paginate(10);
		$logs = Participant::getParticipants($participant_loads);

		return View::make('admin.log.index')
		->with('participant_loads', $participant_loads)
		->with('logs', $logs);
	}
}