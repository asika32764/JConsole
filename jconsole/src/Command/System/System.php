<?php

namespace Command\System;

use Command\System\CleanCache\CleanCache;
use Command\System\Off\Off;
use Command\System\On\On;
use Joomla\Console\Command\Command;

class System extends Command
{
	protected $name = 'system';

	protected $description = 'System control.';

	public $isEnabled = true;

	//        public $usage = 'example <command> [option]';

	public function configure()
	{
		$this
			//->addArgument(new Indexmaker)
			->addArgument(new CleanCache)
			->addArgument(new On)
			->addArgument(new Off);
	}
}
