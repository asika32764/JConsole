<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Command\Build\GenerateCommand;

use JConsole\Command\JCommand;

defined('JCONSOLE') or die;

/**
 * Class GenerateCommand
 *
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @since       3.2
 */
class GenerateCommand extends JCommand
{
	/**
	 * An enabled flag.
	 *
	 * @var bool
	 */
	public static $isEnabled = true;

	/**
	 * Console(Argument) name.
	 *
	 * @var  string
	 */
	protected $name = 'gen-command';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected $description = 'Generate a command class.';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected $usage = 'example <command> [option]';

	/**
	 * Template to generate command.
	 *
	 * @var string
	 */
	protected $template = <<<TMPL
<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Command\{{NAMESPACE}};

use JConsole\Command\JCommand;

defined('JCONSOLE') or die;

/**
 * Class {{CLASS}}
 *
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @since       3.2
 */
class {{CLASS}} extends JCommand
{
	/**
	 * An enabled flag.
	 *
	 * @var bool
	 */
	public static \$isEnabled = true;

	/**
	 * Console(Argument) name.
	 *
	 * @var  string
	 */
	protected \$name = '{{NAME}}';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected \$description = '{{DESCRIPTION}}';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected \$usage = '{{NAME}} <cmd><command></cmd> <option>[option]</option>';

	/**
	 * Configure command information.
	 *
	 * @return void
	 */
	public function configure()
	{
		// \$this->addCommand();

		parent::configure();
	}

	/**
	 * Execute this command.
	 *
	 * @return int|void
	 */
	protected function doExecute()
	{
		return parent::doExecute();
	}
}

TMPL;

	/**
	 * Configure command information.
	 *
	 * @return void
	 */
	public function configure()
	{
		parent::configure();
	}

	/**
	 * Execute this command.
	 *
	 * @return mixed
	 */
	protected function doExecute()
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$name = $this->in("Please enter command name: ");

		$namespace = $this->in("Please enter command namespace: ");

		$description = $this->in("Please enter command description: ");

		if (!$name || !$namespace)
		{
			$this->out('Need name & namespace.');

			return;
		}

		// Regularize Namespace
		$namespace = str_replace(array('/', '\\'), ' ', $namespace);

		$namespace = ucwords($namespace);

		$namespace = str_replace(' ', '\\', $namespace);

		$namespace = explode('\\', $namespace);

		if ($namespace[0] == 'Command')
		{
			array_shift($namespace);
		}

		$class = $namespace;

		$class = array_pop($class);

		$namespace = implode('\\', $namespace);

		$replace = array(
			'{{NAME}}'        => $name,
			'{{NAMESPACE}}'   => $namespace,
			'{{CLASS}}'       => $class,
			'{{DESCRIPTION}}' => $description
		);

		$content = strtr($this->template, $replace);

		$file = JCONSOLE_SOURCE . '/src/Command/' . $namespace . '/' . $class . '.php';

		$file = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $file);

		if (!\JFile::write($file, $content))
		{
			$this->out()->out('Failure when writing file.');

			return false;
		}

		$this->out('File generated: ' . $file);

		return;
	}
}
