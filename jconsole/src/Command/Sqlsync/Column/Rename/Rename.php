<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Command\Sqlsync\Column\Rename;

use JConsole\Command\JCommand;
use Sqlsync\Model\Column;
use Sqlsync\Model\Schema;

defined('JPATH_CLI') or die;

/**
 * Class Rename
 *
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @since       3.2
 */
class Rename extends JCommand
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
	protected $name = 'rename';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected $description = 'Rename column';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected $usage = 'rename <cmd><table name></cmd> <cmd><column name></cmd> <option>[new column name]</option> <option>[option]</option>';

	protected $target = 'column name';

	/**
	 * Configure command information.
	 *
	 * @return void
	 */
	public function configure()
	{
		// $this->addArgument();
	}

	/**
	 * Execute this command.
	 *
	 * @return int|void
	 */
	protected function doExecute()
	{
		@$table = $this->input->args[0];

		@$name = $this->input->args[1];

		@$value = $this->input->args[2];

		if (!$table)
		{
			throw new \InvalidArgumentException("Missing argument <comment>1</comment>: Table name.\n\nUsage:\n" . $this->usage);
		}

		if (!$name)
		{
			throw new \InvalidArgumentException("Missing argument <comment>2</comment>: Column name.\n\nUsage:\n" . $this->usage);
		}

		$schemaModel = new Schema;

		$schema = $schemaModel->getCurrent();

		$column = $schema->get($table . '.columns.' . $name);

		if (!$column)
		{
			throw new \UnexpectedValueException('We are not tracking this table or column of this table not exists.');
		}

		$column = $this->change((array) $column, $value);

		$schema->set($table . '.columns.' . $name, $column);

		$schemaModel->saveVersion(null, $schema);

		$this->out()->out("New {$this->target} saved.");

		return true;
	}

	protected function change($column, $value)
	{
		if (isset($column['Rename']))
		{
			$this->out()->out(sprintf('Current rename setting is: %s => %s', $column['Field'], $column['Rename']));
		}

		$value = $value ?: $this->out()->in('Enter new name:');

		if (!$value)
		{
			throw new \Exception('Cancelled.');
		}

		$column['Rename'] = $value;

		return $column;
	}
}
