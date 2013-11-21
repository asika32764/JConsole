<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Command\Sqlsync\Column\Type;

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
class Type extends Rename
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
	protected $name = 'type';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected $description = 'Set type and length';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected $usage = 'rename <cmd><table name></cmd> <cmd><column name></cmd> <option>[new type]</option> <option>[option]</option>';

	protected $target = 'type';

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
		$this->out()->out(sprintf('Current is: %s', $column['Type']));

		$valid = true;

		do
		{
			if (!$valid)
			{
				$this->out()->out('Invalid value.');
			}

			$value = $this->in('Enter new type: ');
		}

		while (!($valid = TypeValidator::validate($value)));

		if (!$value)
		{
			throw new \Exception('Cancelled.');
		}

		$column['Type'] = $value;

		return $column;
	}
}
