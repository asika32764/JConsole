<?php
/**
 * Part of the Joomla Framework Console Package
 *
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace JConsole\Command;

use Joomla\Console\Command\AbstractCommand;
use Joomla\Console\Command\Command;
use Joomla\Application\Cli\CliOutput;
use Joomla\Input;

/**
 * Base JCommand class.
 *
 * @since  1.0
 */
abstract class JCommand extends Command
{
	/**
	 * Console constructor.
	 *
	 * @param   string           $name    Console name.
	 * @param   Input\Cli        $input   Cli input object.
	 * @param   CliOutput        $output  Cli output object.
	 * @param   AbstractCommand  $parent  Parent Console.
	 *
	 * @throws \LogicException
	 *
	 * @since  1.0
	 */
	public function __construct($name = null, Input\Cli $input = null, CliOutput $output = null, AbstractCommand $parent = null)
	{
		parent::__construct($name, $input, $output, $parent);

		$ref = new \ReflectionClass($this);

		// Register sub commands
		$dirs = new \DirectoryIterator(dirname($ref->getFileName()));

		foreach ($dirs as $dir)
		{
			if (!$dir->isDir() || $dirs->isDot())
			{
				continue;
			}

			$name = ucfirst($dir->getBasename());

			$class = $ref->getNamespaceName() . '\\' . $name . "\\" . $name;

			if (class_exists($class) && $class::$isEnabled)
			{
				$this->addArgument(new $class);
			}
		}
	}

	/**
	 * Configure command.
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	protected function configure()
	{
		$context = get_class($this);

		\JFactory::getApplication()->triggerEvent('onConsoleLoadCommand', array($context, $this));
	}
}
