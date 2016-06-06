<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class DelPermission extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'user:delperm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delate a permission from a user';

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
        if ($user = User::where('member_id', '=', $this->argument('member_id'))->first()) {
	        $user->deletePerm(strtoupper($this->argument('perm')));
	} else {
		$this->error($this->argument('member_id') . ' not found!');
	}
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            [ 'member_id', InputArgument::REQUIRED, 'The user\'s TRMN number' ],
            [ 'perm', InputArgument::REQUIRED, 'The permission to add' ],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [ ];
    }

}
