<?php
/**
 * Part of JConsole project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace JConsole\Installer;

use Composer\Script\CommandEvent;
use Joomla\Filesystem\Folder;

/**
 * Class ComposerInstaller
 *
 * @since 1.0
 */
class ComposerInstaller
{
	/**
	 * Property binFile.
	 *
	 * @var  string
	 */
	static protected $binFile = <<<BIN
#!/usr/bin/env sh
<?php

include_once dirname(__DIR__) . '/libraries/jconsole/bin/console.php';

BIN;

	/**
	 * install
	 *
	 * @param CommandEvent $event
	 *
	 * @return  void
	 */
	public static function install(CommandEvent $event)
	{
		$path = getcwd();

		$io = $event->getIO();

		// Create console file.
		$io->write('Writing console file to bin.');

		file_put_contents($path . '/../../cli/console', static::$binFile);

		// Resource dir
		$resourceDir = $path . '/../../cli/jconsole';

		if (!is_dir($resourceDir))
		{
			$io->write('Create jconsole folder: ' . realpath($resourceDir));

			define('JPATH_ROOT', realpath($path . '/../..'));

			Folder::copy($path . '/resource/jconsole.dist', $resourceDir);
		}

		// Complete
		$io->write('Install complete.');
	}
}
