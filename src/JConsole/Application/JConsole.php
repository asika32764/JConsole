<?php


namespace JConsole\Application;

use JConsole\Descriptor\JOptionDescriptor;
use Joomla\Application\Cli\CliOutput;
use Joomla\Console\Console as JoomlaConsole;
use Joomla\Application\Cli\Output;
use Joomla\Input;
use Joomla\Registry\Registry;

/**
 * Class JConsole
 *
 * @since  3.2
 */
class JConsole extends JoomlaConsole
{
	/**
	 * The application dispatcher object.
	 *
	 * @var    \JEventDispatcher
	 * @since  3.2
	 */
	protected $dispatcher;

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
		$this->loadDispatcher();

		\JFactory::$application = $this;

		parent::__construct($input, $config, $output);

		// Make Windows no ANSI color
		if (defined('PHP_WINDOWS_VERSION_BUILD'))
		{
			$this->input->set('no-ansi', true);
		}

		$descriptorHelper = $this->rootCommand->getChild('help')
			->getDescriptor();

		$descriptorHelper->setOptionDescriptor(new JOptionDescriptor);

		$this->loadFirstlevelCommands();
	}

	/**
	 * loadFirstlevelCommands
	 *
	 * @return void
	 */
	protected function loadFirstlevelCommands()
	{
		try
		{
			\JPluginHelper::importPlugin('console');
		}
		catch (\RuntimeException $e)
		{
			// Do nothing
		}

		// Find commands in cli
		$dirs = new \AppendIterator;

		if (is_dir(JCONSOLE_SOURCE . '/src/Command'))
		{
			$dirs->append(new \DirectoryIterator(JCONSOLE_SOURCE . '/src/Command'));
		}

		$dirs->append(new \DirectoryIterator(JCONSOLE . '/src/Command'));

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
				$this->rootCommand->addCommand(new $class(null, $this->input, $this->output));
			}
		}

		$context = get_class($this->rootCommand);

		$this->triggerEvent('onConsoleLoadCommand', array($context, $this->rootCommand));
	}

	/**
	 * Allows the application to load a custom or default dispatcher.
	 *
	 * The logic and options for creating this object are adequately generic for default cases
	 * but for many applications it will make sense to override this method and create event
	 * dispatchers, if required, based on more specific needs.
	 *
	 * @param   \JEventDispatcher  $dispatcher  An optional dispatcher object. If omitted, the factory dispatcher is created.
	 *
	 * @return  JConsole This method is chainable.
	 *
	 * @since   12.1
	 */
	public function loadDispatcher(\JEventDispatcher $dispatcher = null)
	{
		$this->dispatcher = ($dispatcher === null) ? \JEventDispatcher::getInstance() : $dispatcher;

		return $this;
	}

	/**
	 * Calls all handlers associated with an event group.
	 *
	 * @param   string  $event  The event name.
	 * @param   array   $args   An array of arguments (optional).
	 *
	 * @return  array   An array of results from each function call, or null if no dispatcher is defined.
	 *
	 * @since   12.1
	 */
	public function triggerEvent($event, array $args = null)
	{
		if ($this->dispatcher instanceof \JEventDispatcher)
		{
			return $this->dispatcher->trigger($event, $args);
		}

		return null;
	}

	/**
	 * Enqueue a system message.
	 *
	 * @param   string  $msg   The message to enqueue.
	 * @param   string  $type  The message type. Default is message.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public function enqueueMessage($msg, $type = 'message')
	{
		$this->out($msg);
	}

	/**
	 * Is admin interface?
	 *
	 * @return  boolean  True if this application is administrator.
	 *
	 * @since   3.2
	 */
	public function isAdmin()
	{
		return false;
	}

	/**
	 * Is site interface?
	 *
	 * @return  boolean  True if this application is site.
	 *
	 * @since   3.2
	 */
	public function isSite()
	{
		return true;
	}
}
