<?php

namespace Command\System\CleanCache;

use JConsole\Command\JCommand;

class CleanCache extends JCommand
{
	/**
	 * An enabled flag.
	 *
	 * @var bool
	 */
	public static $isEnabled = true;

	protected $name = 'clean-cache';

	protected $description = 'Clean system cache.';

	protected $usage = 'clean-cache <cmd><folder></cmd> <option>[option]</option>';

	protected $offline = 0;

	public function configure()
	{
		parent::configure();
	}

	protected function doExecute()
	{
		jimport('joomla.filesystem.folder');

		$folder = isset($this->input->args[0]) ? $this->input->args[0] : '/';

		$path = JPATH_BASE . '/cache/' . trim($folder, '/\\');

		$path = realpath($path);

		if (!$path)
		{
			$this->out('Path: "' . $folder . '" not found.');

			return;
		}

		$this->out('Cleaning cache files...');

		if ($path != realpath(JPATH_BASE . '/cache'))
		{
			\JFolder::delete($path);
		}
		else
		{
			$files = new \FilesystemIterator($path);

			foreach ($files as $file)
			{
				if ($file->getBasename() == 'index.html'){
					continue;
				}

				if ($file->isFile())
				{
					unlink((string) $file);
				}
				else
				{
					\JFolder::delete((string) $file);
				}
			}
		}

		$this->out(sprintf('Path: %s cleaned.', $path));

		return;
	}
}
