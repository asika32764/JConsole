<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Command\Sqlsync\Table\Track\None;

use Command\Sqlsync\Table\Track\All\All;

defined('JCONSOLE') or die;

/**
 * Class None
 *
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @since       3.2
 */
class None extends All
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
	protected $name = 'none';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected $description = 'Do not track this table.';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected $usage = 'none <cmd><command></cmd> <option>[option]</option>';

	protected $status = 'none';

	/**
	 * Configure command information.
	 *
	 * @return void
	 */
	public function configure()
	{
		parent::configure();
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
