<?php

namespace Command\Sql\Schema;

use Command\Sql\Schema\Init\Init;
use JConsole\Command\JCommand;

class Schema extends JCommand
{
	public $name = 'schema';

	public $description = 'Schema operation.';

	public static $isEnabled = true;

	//        public $usage = 'example <command> [option]';

	public function configure()
	{
		$this->addArgument(new Init);
	}
}
