<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class AvelcaCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'avelca:install';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Avelca Installation.';

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
	public function fire()
	{
		AvelcaController::avelcaInstall($this);
	}

	/**
     * Get the console command options.
     *
     * @return array
     */
	protected function getOptions()
	{
		return array(
			array('nokey', null, InputOption::VALUE_NONE, 'Generate application secret key')
			);
	}

}
