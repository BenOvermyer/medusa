<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class EmailDump extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'email:dump';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Dump email addresses for members';

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
		foreach(User::where('active','=', 1)->where('registration_status','=','Active')->get() as $user) { 
            $today = strtotime(date('Y') . '-' . date('m') . date('d');
            $bDate = $user->dob;
            $adultToday = strtotime('-18 year', $today );
            
            if ( isnull($bDate) ) {
                echo $user->email_address . "*\n";
            } else if ( $bdate == $adultToday ) {
                echo $user->email_address ;
            } else {
                continue;
            }
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
			array('example', InputArgument::REQUIRED, 'An example argument.'),
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
			array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}
