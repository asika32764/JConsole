<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Command\Sqlsync\Import;

use JConsole\Command\JCommand;
use Sqlsync\Model\Database;
use Sqlsync\Model\Schema;

defined('JPATH_CLI') or die;

/**
 * Class Import
 *
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @since       3.2
 */
class Import extends JCommand
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
	protected $name = 'import';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected $description = 'Import a sql file.';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected $usage = 'import <cmd><command></cmd> <option>[option]</option>';

	/**
	 * Configure command information.
	 *
	 * @return void
	 */
	public function configure()
	{
		$this->addOption(
				array('f', 'force'),
				0,
				'Force import all, ignore compare.'
			)
			->addOption(
				array('s', 'sql'),
				0,
				'Use sql format to export'
			);
	}

	/**
	 * Execute this command.
	 *
	 * @return int|void
	 */
	protected function doExecute()
	{
		$type = $this->getOption('s') ? 'sql' : 'yaml';

		$model = new Schema;

		$path = $model->getPath($type);

		if (file_exists($path))
		{
			$yes = $this->out()->in('This action will compare and update your sql schema (y)es|(n)o?');

			if ($yes != 'y' && $yes != 'yes')
			{
				$this->out('cancelled.');

				return;
			}
		}
		else
		{
			throw new \RuntimeException('Schema file not exitst.');
		}

		$force = $this->getOption('f');

		$model->import($force, $type);

		/*
		$model = new Database;

		$list = $model->getExported();

		$file = array_shift($list);

		// Message
		$this->out()->out(sprintf("The newest sql export is: %s", basename($file)));

		$this->out('Importing...');

		// Do importing
		$model->importFromFile($file);

		// Message
		$queries = $model->getState()->get('import.queries', 0);

		$this->out(sprintf("Import success. %s queries executed.", $queries));

		return true;
		*/
	}
}
