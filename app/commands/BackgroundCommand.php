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
		$delayed_jobs = DelayedJob::where('queue','=',1)->orderBy('id', 'DESC')->first();
		if (isset($delayed_jobs)) {
			$delayed_jobs->queue = 0;
			$delayed_jobs->save();

			$status = 0;
			$survey = Survey::where('id', '=', $delayed_jobs->survey_id)->first();

			if ($delayed_jobs->type == 'importfile') 
			{
				// try{
				// 	DB::beginTransaction();
			    $survey->publish = 3;
			    $survey->save();

			    // Load Master Code Data
				  $master_code = MasterCode::loadData($survey->id);

				  $import = Survey::importDataQuery($delayed_jobs,$survey,$master_code);

				  // Delete Header Data
				  $active_delayed_job_id = $delayed_jobs->id;
				  $active_delayed_job = DelayedJob::find($active_delayed_job_id);
				  $active_delayed_job->delete();

			    $question_default = Question::where('is_default','=',1)->where('survey_id','=',$survey->id)->count();
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
			    $survey->publish = 4;
			    $survey->save();

			 //    DB::commit();
				// }
				// catch(\PDOException $e){
		  //     DB::rollback();
		  //   }
			}
			if ($delayed_jobs->type == 'parsingfile') {
				// Load and parsing Excel Data
				$excel_data = Survey::readHeaderCSV($survey,$delayed_jobs);

				// Delete Delayed Jobs
				$delayed_jobs->delete();

				// Update status to category
				$survey->publish = 6;
				$survey->save();
			}
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
