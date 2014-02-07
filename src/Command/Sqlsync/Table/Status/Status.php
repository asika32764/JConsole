<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Command\Sqlsync\Table\Status;


use JConsole\Command\JCommand;
use Sqlsync\Model\Table;
use Sqlsync\Model\Track;

defined('JCONSOLE') or die;

/**
 * Class Status
 *
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @since       3.2
 */
class Status extends JCommand
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
	protected $name = 'status';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected $description = 'Show tracking status.';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected $usage = 'status <cmd><command></cmd> <option>[option]</option>';

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
		$tableModel = new Table;

		$statuses = $tableModel->status();

		$tables = \JArrayHelper::getColumn($statuses, 'table');

		$maxLength = max(array_map('strlen', $tables));

		// Show message
		$this->out()->out('Track Status:')->out();

		$titleSpaces = $maxLength - 5;

		$this->out(sprintf("TABLE NAME %-{$titleSpaces}s STATUS", ''));

		$this->out('---------------------------------------------------------------');

		// List table & status
		foreach ($statuses as $status)
		{
			$spaces = $maxLength - strlen($status['table']) + 4;

			$this->out(sprintf("- %s %-{$spaces}s %s", $status['table'], '', $status['status']));
		}
	}
}
