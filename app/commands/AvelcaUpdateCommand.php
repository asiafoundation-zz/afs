<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class AvelcaUpdateCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'avelca:update';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Update Avelca core modules and themes to the latest.';

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
		$key = $this->argument('key');
		$this->comment("\nYour Secret Key: {$key}\n");
		
		if ( ! $this->confirm("Have you backed up your modifications (if any) ? [yes|No]", false))
		{
			$this->error("\nPlease back up your current modules and theme (if you made modifications) first.");
			return;
		}

		$this->info("Downloading latest update..");

		$host = 'http://platform.avelca.com/patch/';

		/* Themes */
		$url  = $host.'themes/'.$key;
		$path = public_path().'/themes.zip';

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($ch);
		curl_close($ch);

		file_put_contents($path, $data);

		/* assuming file.zip is in the same directory as the executing script. */
		$file = $path;

		/* get the absolute path to $file */
		$path = pathinfo(realpath($file), PATHINFO_DIRNAME);

		$zip = new ZipArchive;
		$res = $zip->open($file);
		if (filesize($file) > 0 && $res === TRUE) {
			File::cleanDirectory($path.'/themes');

			@File::deleteDirectory(public_path().'/__MACOSX');
			
			/* extract it to the path we determined above */
			$zip->extractTo($path);
			$zip->close();
			$this->comment("Themes updated.");
		} else {
			$this->error("Update failed. Please check you key.");
		}
		File::delete($file);

		clearstatcache();

		/* Modules */
		$url  = $host.'modules/'.$key;
		$path = app_path().'/modules.zip';

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($ch);
		curl_close($ch);

		file_put_contents($path, $data);

		/* assuming file.zip is in the same directory as the executing script. */
		$file = $path;

		/* get the absolute path to $file */
		$path = pathinfo(realpath($file), PATHINFO_DIRNAME);

		$zip = new ZipArchive;
		$res = $zip->open($file);
		if (filesize($file) > 0 && $res === TRUE) {
			File::cleanDirectory($path.'/modules');

			@File::deleteDirectory(public_path().'/__MACOSX');
			
			/* extract it to the path we determined above */
			$zip->extractTo($path);
			$zip->close();
			$this->comment("Core modules updated.");
		} else {
			$this->error("Update failed. Please check you key.");
		}
		File::delete($file);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('key', InputArgument::REQUIRED, 'Secret Key'),
			);
	}

}
