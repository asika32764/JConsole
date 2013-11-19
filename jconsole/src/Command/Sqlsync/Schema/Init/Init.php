<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Command\Sqlsync\Schema\Init;

use JConsole\Command\JCommand;
use Sqlsync\Factory;
use Sqlsync\Model\Schema;

defined('JPATH_CLI') or die;

/**
 * Class Init
 *
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @since       3.2
 */
class Init extends JCommand
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
	protected $name = 'init';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected $description = 'Init a schema file';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected $usage = 'init <cmd><command></cmd> <option>[option]</option>';

	protected $version = 'main';

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
		// Hello message
		$this->out('Initialising schema...');

		// Do init
		$schema = new Schema;

		$schema->init();

		$state = $schema->getState();

		// Report
		$this->out();

		$this->out(sprintf('Schema Initialised'));

		$this->out('------------------------------------');

		$this->out()->out(sprintf('%s tables dumped.', $state->get('dump.count.tables', 0)));

		$this->out(sprintf('%s rows dumped.', $state->get('dump.count.rows', 0)));

		$this->out(sprintf('Save schema file to: %s', $state->get('dump.path')));

		return true;
	}
}
