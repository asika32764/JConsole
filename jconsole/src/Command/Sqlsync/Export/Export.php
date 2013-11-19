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
use Joomla\Registry\Registry;
use Sqlsync\Exporter\SqlExporter;
use Sqlsync\Exporter\YamlExporter;
use Sqlsync\Model\Database;

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
			array('y', 'yaml', 'yml'),
			0,
			'Use yaml format to export'
		);
	}

	/**
	 * Execute this command.
	 *
	 * @return int|void
	 */
	protected function doExecute()
	{
		$type = $this->getOption('y') ? 'yaml' : 'sql';

		$model = new Database;

		$model->save($type);



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
