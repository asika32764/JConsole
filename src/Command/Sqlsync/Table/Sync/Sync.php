<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Command\Sqlsync\Table\Sync;

use JConsole\Command\JCommand;
use Sqlsync\Helper\ProfileHelper;
use Sqlsync\Model\Table;
use Sqlsync\Factory;

defined('JCONSOLE') or die;

/**
 * Class Sync
 *
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @since       3.2
 */
class Sync extends JCommand
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
	protected $name = 'sync';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected $description = 'Sync tracking config.';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected $usage = 'sync <cmd><command></cmd> <option>[option]</option>';

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
			'All profiles'
		);
	}

	/**
	 * Execute this command.
	 *
	 * @return int|void
	 */
	protected function doExecute()
	{
		$tableModel = new Table;

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

			$tableModel->sync();

			$path = $tableModel->getState()->get('track.save.path');

			$this->out()->out('Sync all tracking status to: ' . $path);
		}

		return;
	}
}
