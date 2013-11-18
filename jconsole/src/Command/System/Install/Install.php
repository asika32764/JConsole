<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Command\System\Install;

use JConsole\Command\JCommand;

defined('JPATH_CLI') or die;

/**
 * Class Install
 *
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @since       3.2
 */
class Install extends JCommand
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
	protected $name = 'install';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected $description = 'Install Joomla!';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected $usage = 'install <cmd><command></cmd> <option>[option]</option>';

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
		// Register the Installation application
		\JLoader::registerPrefix('Installation', JPATH_INSTALLATION);

		// Register the application's router due to non-standard include
		\JLoader::register('JRouterInstallation', __DIR__ . '/router.php');

		\JFactory::$application = new \JApplicationWeb;

		try
		{
			$options = $this->askOptions();

			$configModel = new \InstallationModelConfiguration;

			$configModel->setup($options);
		}
		catch(\Exception $e)
		{
			echo $e;
		}
	}

	protected function askOptions()
	{


		$options = array();

		$options['site_name'] = $this->in('Site name (Joomla):') ?: 'Joomla';

		$options['admin_email'] = null;

		while(!$options['admin_email'])
		{
			$options['admin_email'] = $this->in('Admin mail *:');
		}

		$options['admin_user'] = $this->in('Admin user (admin):') ?: 'admin';

		$options['admin_password'] = null;

		while(!$options['admin_password'])
		{
			$options['admin_password'] = $this->in('Admin password *:');
		}

		$this->out('
Available driver:
- mysql
- mysqli
- oracle
- postgresql
- sqlsrv
- sqlazure');

		$options['db_type'] = $this->in('Databaase driver (mysql):') ?: 'mysql';

		$options['db_host'] = $this->in('Database location (localhost):') ?: 'localhost';

		$options['db_user'] = $this->in('Database user (root):') ?: 'root';

		$options['db_pass'] = $this->in('Database password') ?: '';

		$options['db_name'] = $this->in('Database name (joomla):') ?: 'joomla';

		$prefix = $this->randPrefix();

		$options['db_prefix'] = $this->in("Database prefix ({$prefix}):") ?: $prefix;

		$options['site_metadesc'] = null;

		$options['site_offline'] = null;

		return $options;
	}

	protected function randPrefix()
	{
		// Create the random prefix:
		$prefix = '';
		$chars = range('a', 'z');
		$numbers = range(0, 9);

		// We want the fist character to be a random letter:
		shuffle($chars);
		$prefix .= $chars[0];

		// Next we combine the numbers and characters to get the other characters:
		$symbols = array_merge($numbers, $chars);
		shuffle($symbols);

		for ($i = 0, $j = 5 - 1; $i < $j; ++$i)
		{
			$prefix .= $symbols[$i];
		}

		// Add in the underscore:
		$prefix .= '_';

		return $prefix;
	}
}
