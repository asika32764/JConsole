<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Command\Sqlsync\Column;

use JConsole\Command\JCommand;

defined('JCONSOLE') or die;

/**
 * Class Column
 *
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @since       3.2
 */
class Column extends JCommand
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
	protected $name = 'col';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected $description = 'Column operation';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected $usage = 'column <cmd><command></cmd> <option>[option]</option>';

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
