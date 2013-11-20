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
	protected $usage = 'rename <cmd><command></cmd> <option>[option]</option>';

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
		$schemaModel = new Schema;

		$schema = $schemaModel->getCurrent();

		$column = $schema->get('#__assets' . '.columns.' . 'id');

		$column = $this->change((array) $column, 'id2');

		$schema->set('#__assets' . '.columns.' . 'id', $column);

		$schemaModel->saveVersion(null, $schema);

		return true;
	}

	protected function change($column, $value)
	{
		$column['Rename'] = $value;

		return $column;
	}
}
