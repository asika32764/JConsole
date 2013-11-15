<?php
/**
 * Created by PhpStorm.
 * User: Ezio
 * Date: 2013/11/14
 * Time: 下午 9:02
 */

namespace Command\System\On;

use Joomla\Console\Command\Command;

class On extends Command
{
	protected $name = 'on';

	protected $description = 'Set this site online.';

	protected $usage = 'on [option]';

	protected $offline = 0;

	public function configure()
	{
	}

	protected function doExecute()
	{
		jimport('joomla.filesystem.file');

		$config = \JFactory::getConfig();

		$config->set('offline', $this->offline);

		$class = $config->toString('php', array('class' => 'JConfig'));

		if (!\JFile::write(JPATH_CONFIGURATION . '/configuration.php', $class))
		{
			throw new \Exception('Writing config fail.');
		}

		$this->out("\nSystem <info>" . strtoupper($this->name) . "</info>");

		return;
	}
}
