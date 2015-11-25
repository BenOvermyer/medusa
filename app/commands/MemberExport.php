<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class MemberExport extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'member:export';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Export selected fields from the member database collection';

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
        $options = $this->option();

        // Build the export header row

        $count = 0;
        foreach(explode(',', str_replace(' ', '', $options['fields'])) as $field) {
            if ($count > 0) {
                echo ',';
            }
            echo $field;
            $count++;
        }

        echo "\n";

		foreach(User::where('active','=', 1)->where('registration_status','=','Active')->get() as $user) {

            if ($options['over18'] === true || $options['under18'] === true) {
                // Do age check
                $today = strtotime(date('Y-m-d'));
                $adultToday = strtotime('-18 year', $today );

                if ((empty($user->dob) === false && strtotime($user->dob) > $adultToday && $options['over18'] === true) ||
                    ($options['under18'] === true && strtotime($user->dob) <= $adultToday)) {
                    continue;
                } elseif(empty($user->dob) === false && $options['noDoB'] === true) {
                    continue;
                }
            }

            // Build the export row

            $count = 0;
            foreach(explode(',', str_replace(' ', '', $options['fields'])) as $field) {
                if ($count > 0) {
                    echo ',';
                }
                if (empty($user->$field) === false) {
                    echo $user->$field;
                }
                $count++;
            }
            echo "\n";
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
		return [
            ['over18', null, InputOption::VALUE_NONE, 'Limit export to members over 18 or with no listed date of birth'],
            ['under18', null, InputOption::VALUE_NONE, 'Limit export to members under 18'],
            ['fields', null, InputOption::VALUE_OPTIONAL, 'Comma separated list of field names to include in the export'],
            ['noDoB', null, InputOption::VALUE_NONE, 'Limit export to members who do not have a date of birth on record'],
        ];
    }
}
