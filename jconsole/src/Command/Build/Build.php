<?php

namespace Command\Build;

use JConsole\Command\JCommand;

class Build extends JCommand
{
	/**
	 * An enabled flag.
	 *
	 * @var bool
	 */
	public static $isEnabled = true;

	protected $name = 'build';

	protected $description = 'Some useful tools for building system.';

	public function configure()
	{
		parent::configure();
	}
}
