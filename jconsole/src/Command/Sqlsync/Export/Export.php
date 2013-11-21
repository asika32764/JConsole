<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Command\Sqlsync\Export;


use JConsole\Command\JCommand;
use Sqlsync\Model\Schema;

defined('JPATH_CLI') or die;

/**
 * Class Export
 *
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @since       3.2
 */
class Export extends JCommand
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
	protected $name = 'export';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected $description = 'Export sql.';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected $usage = 'export <cmd><command></cmd> <option>[option]</option>';

	/**
	 * Configure command information.
	 *
	 * @return void
	 */
	public function configure()
	{
		$this->addOption(
			array('s', 'sql'),
			0,
			'Use sql format to export'
		);
	}

	/**
	 * Execute this command.
	 *
	 * @return int|void
	 */
	protected function doExecute()
	{
		$type = $this->getOption('s') ? 'sql' : 'yaml';

		$model = new Schema;

		$path = $model->getPath($type);

		if (file_exists($path))
		{
			$yes = $this->out()->in('Schema file exists, do you want to override it (y)es|(n)o?');

			if ($yes != 'y' && $yes != 'yes')
			{
				$this->out('cancelled.');

				return;
			}
		}

		$model->export($type);

		$this->out()->out(sprintf('Schema file dumped to: %s', $model->getState()->get('dump.path')));

		/*
		$yaml = $this->getOption('yaml');

		$exporter = $yaml ? new YamlExporter : new SqlExporter;

		$result = $exporter->export();

		$config = \JFactory::getConfig();

		$file = 'site-' . $config->get('db') . '-' . date('Y-m-d-H-i-s');

		if ($yaml)
		{
			$file .= '.yml';
		}
		else
		{
			$file .= '.sql';
		}

		$file = JPATH_CLI . '/jconsole/resource/sql/export/' . ($yaml ? 'yaml/' : '') . $file;

		\JFile::write($file, $result);

		$this->out()->out(sprintf('Sql file dumped to: %s', $file));
		*/
	}
}
