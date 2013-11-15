<?php

namespace Command\System\CleanCache;

use Joomla\Console\Command\Command;

class CleanCache extends Command
{
	protected $name = 'clean-cache';

	protected $description = 'Clear system cache.';

	protected $usage = 'clean-cache <cmd><folder></cmd> <option>[option]</option>';

	protected $offline = 0;

	public function configure()
	{
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
