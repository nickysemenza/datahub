<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use App\Http\Controllers\FBChatController;
use App\Models\Threads;
class DataUpdate extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'data:update';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';

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
		if(($this->option('getThreads')) != null)
		{
			echo("hi");
			$controller->getFBThreads();
		}
		if(($this->option('all')) != null)
		{
			$this->info('Display this on the screen');
			$controller->updateMessageCount();
		}
		if(($this->option('thread')) != null)
		{
			$thread_id=($this->option('thread'));
			echo("Now Updating Thread ".$thread_id);
			$controller->getFBMessagesFromThread($thread_id);
		}
		if(($this->option('test1')) != null)
		{


			$threads = Threads::orderBy('message_count','DESC')->get();
			foreach($threads as $eachThread)
			{
				$thread_id = $eachThread['thread_id'];
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
		return [
			['thread', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
			['test1', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
			['all', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
			['getThreads', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
		];
	}

}
