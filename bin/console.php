<?php
/**
* @package     Joomla.Cli
*
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

// We are a valid entry point.
const _JEXEC = 1;

// Configure error reporting to maximum for CLI output.
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load system defines
if (file_exists(dirname(__DIR__) . '/../../defines.php'))
{
	require_once dirname(__DIR__) . '/../../defines.php';
}

if (!defined('_JDEFINES'))
{
	define('JPATH_BASE', realpath(dirname(__DIR__) . '/../..'));
	require_once JPATH_BASE . '/includes/defines.php';
}

define('JCONSOLE', dirname(__DIR__));
define('JCONSOLE_SOURCE', JPATH_ROOT . '/cli/jconsole');

// Get the framework.
require_once JPATH_BASE . '/includes/framework.php';

// Bootstrap the CMS libraries.
require_once JPATH_LIBRARIES . '/cms.php';

restore_exception_handler();
restore_error_handler();

// Load Console libraries
include_once JCONSOLE . '/vendor/autoload.php';

// Import the configuration.
require_once JPATH_CONFIGURATION . '/configuration.php';

// Set error handler
set_error_handler(array('\\JConsole\\Error\\ErrorHandler', 'handleError'));
set_exception_handler(array('\\JConsole\\Error\\ErrorHandler', 'handleException'));

// System configuration.
$config = new JConfig;

use JConsole\Application\JConsole;
use Joomla\Console\Output\Stdout;

$console = new JConsole(null, null, new Stdout);

$console->setDescription(null)
	->execute();
