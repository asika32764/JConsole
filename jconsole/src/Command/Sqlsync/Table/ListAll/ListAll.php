<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Command\Sqlsync\Table\ListAll;

use JConsole\Command\JCommand;

defined('JPATH_CLI') or die;

/**
 * Class ListAll
 *
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @since       3.2
 */
class ListAll extends JCommand
{
	/**
	 * An enabled flag.
	 *
	 * @var bool
	 */
	public static $isEnabled = true;

	/**
	 * Console(Argument) name.
	 *
	 * @var  string
	 */
	protected $name = 'list';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected $description = 'List all tables.';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected $usage = 'list <cmd><command></cmd> <option>[option]</option>';

	/**
	 * Configure command information.
	 *
	 * @return void
	 */
	public function configure()
	{
		$this->addOption(
			array('a', 'all'),
			0,
			'List all includes different prefix.'
		);
	}

	/**
	 * Execute this command.
	 *
	 * @return int|void
	 */
	protected function doExecute()
	{
		$db = \JFactory::getDbo();

		$prefix = $db->getPrefix();

		// Show list tables
		$sql = 'SHOW TABLES';

		if (!$this->getOption('a'))
		{
			$sql .= " LIKE '{$prefix}%'";
		}

		$tables = $db->setQuery($sql)->loadColumn();

		foreach ($tables as $table)
		{
			$this->out('- ' . $table);
		}

		// Count all tables in this db
		$count = count($db->setQuery('SHOW TABLES')->loadColumn());

		// Output1
		$this->out();

		$this->out('List tables: ' . count($tables));

		$this->out('All tables in this database: ' . $count);
	}
}
