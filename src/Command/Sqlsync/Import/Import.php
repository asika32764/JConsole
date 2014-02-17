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
use Joomla\Console\Prompter\BooleanPrompter;
use Sqlsync\Factory;
use Sqlsync\Helper\ProfileHelper;
use Sqlsync\Model\Database;
use Sqlsync\Model\Schema;

defined('JCONSOLE') or die;

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
		$this
			->addOption(
				array('f', 'force'),
				0,
				'Force import all, ignore compare.'
			)
			->addOption(
				array('s', 'sql'),
				0,
				'Use sql format to export'
			)->addOption(
				array('a', 'all'),
				0,
				'All profiles'
			);
	}

	/**
	 * Execute this command.
	 *
	 * @throws \RuntimeException
	 * @return int|void
	 */
	protected function doExecute()
	{
		$type = $this->getOption('s') ? 'sql' : 'yaml';

		$model = new Schema;

		$path = $model->getPath($type);

		if (file_exists($path) && !$this->getOption('y'))
		{
			$prompter = new BooleanPrompter('This action will compare and update your sql schema [Y/n]: ');

			if (!$prompter->ask())
			{
				$this->out('cancelled.');

				return;
			}
		}
		elseif (!file_exists($path))
		{
			throw new \RuntimeException('Schema file not exists.');
		}

		$force = $this->getOption('f');

		if ($force)
		{
			throw new \RuntimeException('Sorry, force mode not prepare yet...');
		}

		$state = $model->getState();

		if ($this->getOption('a'))
		{
			$profiles = ProfileHelper::getAllProfiles();
		}
		else
		{
			$profiles = $this->input->args ? : array(ProfileHelper::getProfile());
		}

		$config = Factory::getConfig();

		foreach ($profiles as $profile)
		{
			$config->set('profile', $profile);

			// Backup
			$this->out()->out(sprintf('<comment>Backing up</comment> profile <info>%s</info> ...', $profile));

			$model->backup();

			$this->out()->out(sprintf('Schema file backup to: %s', $model->getState()->get('dump.path')));

			// Import

			$this->out()->out(sprintf('<option>Importing</option> profile schema: <info>%s</info> ...', $profile));

			$model->import($force, $type);

			$this->out()->out(sprintf('Schema file dumped to: %s', $model->getState()->get('dump.path')));

			// Report
			$analyze = $state->get('import.analyze');

			foreach ($analyze as $table => $schema)
			{
				$this->out()->out($table . ':');

				foreach ($schema as $action => $count)
				{
					$this->out('    ' . $action . ': ' . $count);
				}
			}
		}

		return;
	}
}
