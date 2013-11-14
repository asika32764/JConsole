<?php

namespace Command\Build;

use Command\Build\CheckPHP\CheckPHP;
use Command\Build\Indexmaker\Indexmaker;
use Joomla\Console\Command\Command;

class Build extends Command
{
	protected $name = 'build';

	protected $description = 'Some useful tools for building system.';

	public $isEnabled = true;

	//        public $usage = 'example <command> [option]';

	public function configure()
	{
		$this
			->addArgument(new Indexmaker)
			->addArgument(new CheckPHP);
	}
}
