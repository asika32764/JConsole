<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Command\Sqlsync\Restore;

use JConsole\Command\JCommand;
use Joomla\Console\Prompter\BooleanPrompter;
use Sqlsync\Model\Schema;

defined('JCONSOLE') or die;

/**
 * Class Restore
 *
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @since       3.2
 */
class Restore extends JCommand
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
	protected $name = 'restore';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected $description = 'Restore to pervious point.';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected $usage = 'restore <cmd><command></cmd> <option>[option]</option>';

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
	 * @throws \RuntimeException
	 * @return int|void
	 */
	protected function doExecute()
	{
		$model = new Schema;

		$path = $model->backupPath;

		if (file_exists($path) && !$this->getOption('y'))
		{
			$prompter = new BooleanPrompter('Are you sure you want to restore? [Y/n]: ');

			if (!$prompter->ask())
			{
				$this->out('cancelled.');

				return;
			}
		}

		if (!file_exists($path))
		{
			throw new \RuntimeException('Backup file not exists.');
		}

		$this->out()->out('Restoring...');

		$model->restore();

		$state = $model->getState();

		$queries = $state->get('import.queries');

		$this->out()->out(sprintf('Restore success, %s queries executed.', $queries));

		return;
	}
}
