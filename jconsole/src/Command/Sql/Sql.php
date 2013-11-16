<?php

namespace Command\Sql;

use JConsole\Command\JCommand;
use Sqlsync\Schema;
use Symfony\Component\Yaml\Dumper as SymfonyYamlDumper;

class Sql extends JCommand
{
	public $name = 'sql';

	public $description = 'Example description.';

	public static $isEnabled = true;

	//        public $usage = 'example <command> [option]';

	public function configure()
	{
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
