<?php

namespace Command\System;

use JConsole\Command\JCommand;

class System extends JCommand
{
	/**
	 * An enabled flag.
	 *
	 * @var bool
	 */
	public static $isEnabled = true;

	protected $name = 'system';

	protected $description = 'System control.';
}
