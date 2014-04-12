<?php
/**
 * Part of JConsole project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace JConsole\Error;

use JConsole\Command\ErrorCommand;

/**
 * Class ErrorHandler
 *
 * @since 1.0
 */
abstract class ErrorHandler
{
	/**
	 * Converts generic PHP errors to \ErrorException
	 * instances, before passing them off to be handled.
	 *
	 * This method MUST be compatible with set_error_handler.
	 *
	 * @param int    $level
	 * @param string $message
	 * @param string $file
	 * @param int    $line
	 *
	 * @throws \ErrorException
	 * @return bool
	 */
	public static function handleError($level, $message, $file = null, $line = null)
	{
		if ($level & error_reporting())
		{
			$exception = new \ErrorException($message, $level, 0, $file, $line);

			$command = new ErrorCommand;

			$command->renderException($exception);

			die;
		}
	}

	/**
	 * handleException
	 *
	 * @param \Exception $exception
	 *
	 * @return  void
	 */
	public static function handleException(\Exception $exception)
	{
		$command = new ErrorCommand;

		$command->renderException($exception);

		die;
	}
}
