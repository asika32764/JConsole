<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Command\Sqlsync\Column\Null;

use Command\Sqlsync\Column\Rename\Rename;
use Sqlsync\Helper\TypeValidator;

defined('JPATH_CLI') or die;

/**
 * Class Type
 *
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @since       3.2
 */
class Null extends Rename
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
	protected $name = 'null';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected $description = 'Set allow null.';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected $usage = 'null <cmd><table name></cmd> <cmd><column name></cmd> <option>[allow null]</option> <option>[option]</option>';

	protected $target = 'ALLOW NULL';

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
		return parent::doExecute();
	}

	protected function change($column, $value)
	{
		if (!$value)
		{
			$this->out()->out(sprintf('Current ALLOW NULL is: %s', $column['Null']));

			$value = $this->out()->in('Setting ALLOW NULL (y|yes) or (n|no): ');
		}

		if (!$value)
		{
			throw new \Exception('Cancelled.');
		}
		elseif ($value == 'y' || $value == 'yes')
		{
			$value = 'YES';
		}
		else
		{
			$value = 'NO';
		}

		$column['Null'] = $value;

		return $column;
	}
}
