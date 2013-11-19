<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Command\Sqlsync\Column\Change;

use JConsole\Command\JCommand;
use Sqlsync\Model\Column;

defined('JPATH_CLI') or die;

/**
 * Class Change
 *
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @since       3.2
 */
class Change extends JCommand
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
	protected $name = 'change';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected $description = 'Change column schema.';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected $usage = 'change <cmd><command></cmd> <option>[option]</option>';

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
		$model = new Column;

		$column = $model->getColumnSchema('#__assets', 'id');

		$schema = $this->ask($column);
	}

	protected function ask($column)
	{
		$column->Rename = $this->in("New column name, empty skip:");

		$column->Type = $this->in("New column name, empty skip:");

		$column->Null = $this->in("New column name, empty skip:");

		$column->Rename = $this->in("New column name, empty skip:");

		$column->Rename = $this->in("New column name, empty skip:");

		$column->Rename = $this->in("New column name, empty skip:");
	}
}
