<?php


namespace JConsole\Application;


use JConsole\Descriptor\JOptionDescriptor;
use Joomla\Console\Command\DefaultCommand;
use Joomla\Console\Console as JoomlaConsole;
use Joomla\Application\Cli\Output;
use Joomla\Input;

class Console extends JoomlaConsole
{
	/**
	 * Register default command.
	 *
	 * @return  Console  Return this object to support chaining.
	 *
	 * @since  1.0
	 */
	public function registerDefaultCommand()
	{
		$this->defaultCommand = new DefaultCommand(null, $this->input, $this->output);

		$this->defaultCommand->setApplication($this);

		$descriptorHelper = $this->defaultCommand->getArgument('help')
			->getDescriptor();

		$descriptorHelper->setOptionDescriptor(new JOptionDescriptor);

		return $this;
	}
}
