<?php

namespace Command\System;

use Joomla\Console\Command\Command;

class System extends Command
{
	protected $name = 'system';

	protected $description = 'System control.';

	public $isEnabled = true;

	//        public $usage = 'example <command> [option]';

	public function configure()
	{
		//$this
			//->addArgument(new Indexmaker)
			//->addArgument(new CheckPHP);
	}
}
