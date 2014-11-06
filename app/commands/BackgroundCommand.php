<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class BackgroundCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'background:process';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Daemon for background process.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
 /**
  * Execute the console command.
  *
  * @return mixed
  */
	public function fire()
	{
		$sleep = 60;
		while (true) {
			$delayed_jobs = DelayedJob::where('type','=','importfile')->orderBy('id', 'DESC')->first();
			if (isset($delayed_jobs)) {
				$data_parse = json_decode($delayed_jobs->data);

			  $data = array();
			  $data['survey_id'] = $data_parse->survey_id;
			  foreach ($data_parse->options_selected as $key => $value) {
			    $data['options_selected'][$key] = (array)$value;
			  }

			  $status = 0;
			  $survey = Survey::where('id', '=', $data['survey_id'])->first();
			  // save code
			  $codes = MasterCode::savingProcess($data);
			  // Load Master Code Data
			  $master_code = MasterCode::loadData($data);
			  // Load Excel Data
			  $excel_data = Survey::readHeader($survey->baseline_file, 'BZ', 1);

			  $count_excel_data = count($excel_data);
			  $next_queue = $delayed_jobs->queue + 100;

			  $excel_data_chop = array_slice($excel_data, $delayed_jobs->queue, 100);
			  // Saving queue data
			  $delayed_jobs->information = $count_excel_data;
			  $delayed_jobs->queue = $next_queue;
			  $delayed_jobs->save();

			  // Import Data
			  $status = Survey::importData($survey,$master_code,$excel_data_chop);
			  // delete jobs
			  if ($next_queue >= $count_excel_data) {
			    $delayed_jobs->delete();

			    // Update publish status
			    $survey->publish = 3;
			    $survey->save();
			  }
			}
		  echo "Sleep for ".$sleep." seconds...\n";
		  sleep($sleep);
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			// array('example', InputArgument::REQUIRED, 'An example argument.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			// array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}
