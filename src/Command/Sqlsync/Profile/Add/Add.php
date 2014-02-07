<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Command\Sqlsync\Profile\Add;

use JConsole\Command\JCommand;
use Sqlsync\Model\Profile\ProfileModel;

defined('JCONSOLE') or die;

/**
 * Class Add
 *
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @since       3.2
 */
class Add extends JCommand
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
	protected $name = 'add';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected $description = 'Add a new profile';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected $usage = 'add <cmd><command></cmd> <option>[option]</option>';

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
		@$name = $this->input->args[0];

		if (!$name)
		{
			throw new \Exception('Please enter a profile name.');
		}

		$model = new ProfileModel;

		$model->add($name);

		$this->out()->out(sprintf("Profile \"%s\" created.", $name));

		return true;
	}
}
