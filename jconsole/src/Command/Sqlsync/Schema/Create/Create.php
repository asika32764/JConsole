<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Command\Sqlsync\Schema\Create;

use JConsole\Command\JCommand;
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
class Create extends JCommand
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
	protected $name = 'create';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected $description = 'Create a schema version';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected $usage = 'create <cmd><command></cmd> <option>[option]</option>';

	protected $version = null;

	/**
	 * Configure command information.
	 *
	 * @return void
	 */
	public function configure()
	{
		$this->addOption(
				array('f', 'force'),
				0,
				'Force new version.'
			);
	}

	/**
	 * Execute this command.
	 *
	 * @return int|void
	 */
	protected function doExecute()
	{
		// Hello message
		$this->out('Creating new version...');

		// Do create.
		$schema = new Schema;

		if (!$schema->hasInit())
		{
			$this->out()->out('This profile not initialised yet, we are initialising it first...');

			$this->getParent()->getArgument('init')->execute();

			$this->out()->out('Initialised ok. Then we create new version.');
		}

		$schema->create($this->getOption('f'));

		$version = $schema->getCurrentVersion();

		$state = $schema->getState();

		// Report
		$this->out();

		$this->out(sprintf('Generated new version: %s', $version));

		$this->out('------------------------------------');

		$this->out()->out(sprintf('%s tables dumped.', $state->get('dump.count.tables', 0)));

		$this->out(sprintf('%s rows dumped.', $state->get('dump.count.rows', 0)));

		$this->out(sprintf('Save schema file to: %s', $state->get('dump.path')));

		return true;
	}
}
