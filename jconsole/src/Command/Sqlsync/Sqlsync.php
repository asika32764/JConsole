<?php

namespace Command\Sqlsync;

use JConsole\Command\JCommand;
use Sqlsync\Schema;
use Symfony\Component\Yaml\Dumper as SymfonyYamlDumper;

class Sqlsync extends JCommand
{
	public $name = 'sql';

	public $description = 'Example description.';

	public static $isEnabled = true;

	//        public $usage = 'example <command> [option]';

	public function configure()
	{
		define('SQLSYNC_COMMAND',  __DIR__);

		define('SQLSYNC_RESOURCE', JPATH_CLI . '/jconsole/resource/sqlsync');

		define('SQLSYNC_PROFILE',  SQLSYNC_RESOURCE . '/profiles');

		define('SQLSYNC_LIB',      JPATH_CLI . '/jconsole/vendor/Sqlsync');
	}

	protected function doExecute()
	{
		$this->testSchema();
		/*$dumper = new SymfonyYamlDumper;

		$db = \JFactory::getDbo();

		$table = $db->setQuery('SHOW FULL COLUMNS FROM #__content')->loadAssocList('Field');

		$create = $db->setQuery('SHOW CREATE TABLE #__content')->loadAssocList();

		print_r($create);

		//echo $dumper->dump(json_decode(json_encode($table), true), 2, 0, false, true);*/
	}

	protected function testSchema()
	{
		$schema = new Schema(\JFactory::getDbo());

		$schema->getCurrentVersion();
	}
}
