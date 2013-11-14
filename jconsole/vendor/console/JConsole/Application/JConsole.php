<?php


namespace JConsole\Application;


use JConsole\Descriptor\JOptionDescriptor;
use Joomla\Application\Cli\CliOutput;
use Joomla\Console\Command\DefaultCommand;
use Joomla\Console\Console as JoomlaConsole;
use Joomla\Application\Cli\Output;
use Joomla\Input;
use Joomla\Registry\Registry;

class JConsole extends JoomlaConsole
{
	/**
	 * Class constructor.
	 *
	 * @param   Input\Cli  $input   An optional argument to provide dependency injection for the application's
	 *                              input object.  If the argument is a InputCli object that object will become
	 *                              the application's input object, otherwise a default input object is created.
	 *
	 * @param   Registry   $config  An optional argument to provide dependency injection for the application's
	 *                              config object.  If the argument is a Registry object that object will become
	 *                              the application's config object, otherwise a default config object is created.
	 *
	 * @param   CliOutput  $output  The output handler.
	 *
	 * @since   1.0
	 */
	public function __construct(Input\Cli $input = null, Registry $config = null, CliOutput $output = null)
	{
		parent::__construct($input, $config, $output);

		$descriptorHelper = $this->defaultCommand->getArgument('help')
			->getDescriptor();

		$descriptorHelper->setOptionDescriptor(new JOptionDescriptor);

		$this->loadFirstlevelCommands();
	}

	protected function loadFirstlevelCommands()
	{
		$command = $this->defaultCommand;

		// Find commands in cli
		$dirs = new \DirectoryIterator(JPATH_BASE . '/cli/jconsole/src/Command');

		foreach ($dirs as $dir)
		{
			if (!$dir->isDir())
			{
				continue;
			}

			$name = ucfirst($dir->getBasename());

			$class = "Command\\" . $name . "\\" . $name;

			if (class_exists($class))
			{
				$this->defaultCommand->addArgument(new $class);
			}
		}

	}
}
