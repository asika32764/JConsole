<?php

namespace Command\Asset;

use JConsole\Command\JCommand;

/**
 * Class Asset
 *
 * @package Command
 */
class Asset extends JCommand
{
	public $name = 'asset';

	public $description = 'Asset tools.';

	public static $isEnabled = true;
}
