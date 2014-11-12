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
		set_time_limit(60);
		
		// $sleep = 60;
		// while (true) {
			$delayed_jobs = DelayedJob::where('type','=','importfile')->where('queue','=',1)->orderBy('id', 'DESC')->first();
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
			  // Saving queue data
			  $delayed_jobs->information = $count_excel_data;
			  $delayed_jobs->queue = 0;
			  $delayed_jobs->save();

			  $active_delayed_job_id = $delayed_jobs->id;

			  // Import Data
			  $status = Survey::importData($survey,$master_code,$excel_data);

			  DB::table('delayed_jobs')->where('id','=',$active_delayed_job_id)->delete();

		    $question_default = Question::where('is_default','=',1)->count();
		    if ($question_default == 0) {
		    	$default_question_query = Question::select('questions.id','answers.cycle_id')->join('question_categories', 'question_categories.id','=','questions.question_category_id')->join('answers', 'answers.question_id','=','questions.id')->where('question_categories.survey_id','=',$survey->id)->orderBy('questions.id', 'DESC')->first();

		    	$default_question = Question::where('id','=',$default_question_query->id)->first();
		    	$default_question->is_default = 1;
		    	$default_question->save();

		    	$answer_default = DB::table('answers')
		    		->where('question_id', $default_question_query->id)
		    		->where('cycle_id', $default_question_query->cycle_id)
		    		->update(array(
		    			'cycle_default' => 1
		    		));
		    }

		    // Update publish status
		    $survey->publish = 3;
		    $survey->save();
			}
		  // echo "Sleep for ".$sleep." seconds...\n";
		  // sleep($sleep);
		// }
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
