<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Command\Sqlsync\Table\Track;

use JConsole\Command\JCommand;

defined('JCONSOLE') or die;

/**
 * Class Track
 *
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @since       3.2
 */
class Track extends JCommand
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
	protected $name = 'track';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected $description = 'Set track status of table.';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected $usage = 'track <cmd>[all|cols|none]</cmd> <comment><table_name></comment> <option>[option]</option>';

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
}
