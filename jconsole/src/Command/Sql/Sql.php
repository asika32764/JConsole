<?php

namespace Command\Sql;

use Joomla\Console\Command\Command;
use Symfony\Component\Yaml\Dumper as SymfonyYamlDumper;

class Sql extends Command
{
	public $name = 'sql';

	public $description = 'Example description.';

	public $isEnabled = true;

	//        public $usage = 'example <command> [option]';

	public function configure()
	{
		/*
		$this->addArgument(new ExampleCommand)
				->addOption(
						'a',
						0,
						'desc'
				);
		*/
	}

	protected function doExecute()
	{
		$dumper = new SymfonyYamlDumper;

		$db = \JFactory::getDbo();

		$table = $db->setQuery('SHOW FULL COLUMNS FROM #__content')->loadAssocList('Field');

		$create = $db->setQuery('SHOW CREATE TABLE #__content')->loadAssocList();

		print_r($create);

		//echo $dumper->dump(json_decode(json_encode($table), true), 2, 0, false, true);
	}
}
