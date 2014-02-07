<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Command\Sqlsync\Profile\Remove;

use JConsole\Command\JCommand;
use Joomla\Console\Prompter\BooleanPrompter;
use Sqlsync\Model\Profile\ProfileModel;

defined('JCONSOLE') or die;

/**
 * Class Remove
 *
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @since       3.2
 */
class Remove extends JCommand
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
	protected $name = 'rm';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected $description = 'Remove a profile.';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected $usage = 'rm <cmd><command></cmd> <option>[option]</option>';

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
	 * @throws \Exception
	 * @return int|void
	 */
	protected function doExecute()
	{
		@$name = $this->input->args[0];

		if (!$name)
		{
			throw new \Exception('Please enter a profile name.');
		}

		if (!$this->getOption('y'))
		{
			$prompter = new BooleanPrompter('Do you really want to remove "' . $name . '" profile? [Y/n]: ');

			if (!$prompter->ask())
			{
				return false;
			}
		}

		$model = new ProfileModel;

		$model->remove($name);

		$this->out()->out(sprintf('Profile "%s" removed.', $name));

		return true;
	}
}
