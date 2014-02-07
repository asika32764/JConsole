<?php

namespace Command\Sqlsync;

use JConsole\Command\JCommand;
use Sqlsync\Schema;
use Symfony\Component\Yaml\Dumper as SymfonyYamlDumper;

class Sqlsync extends JCommand
{
	public $name = 'sql';

	public $description = 'SQL migration tools.';

	public static $isEnabled = true;

	//        public $usage = 'example <command> [option]';

	public function configure()
	{
		parent::configure();
	}

	public function execute()
	{
		define('SQLSYNC_COMMAND',  __DIR__);

		define('SQLSYNC_RESOURCE', JCONSOLE_SOURCE . '/resource/sqlsync');

		define('SQLSYNC_PROFILE',  SQLSYNC_RESOURCE);

		define('SQLSYNC_LIB',      JCONSOLE . '/src/Sqlsync');

		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		return parent::execute();
	}
}
