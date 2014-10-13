<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class dataupdate extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'command:dataupdate';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Updates the data.';

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
        $controller = new FBChatController();
        //$controller->updateEverything();
        //$controller->getFBThreads();
        if(($this->option('all')) != null)
        {
            $controller->updateEverything();
        }
        if(($this->option('thread')) != null)
        {
            $thread_id=($this->option('thread'));
            echo("Now Updating Thread ".$thread_id);
            $controller->getFBMessagesFromThread($thread_id);
        }
        if(($this->option('test1')) != null)
        {
            $threads = array('t_mid.1387177795070:9d730af31f6fa7b260','t_id.380785408616428','t_mid.1376030520370:74922e8c85d2b6f516');
                foreach($threads as $thread_id)
            {
                echo("Now Updating Thread ".$thread_id);
                $controller->getFBMessagesFromThread($thread_id);
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
        return [];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('thread', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
            array('test1', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
            array('all', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
        );
    }

}
