<?php

namespace Command\Build\Indexmaker;

use JConsole\Command\JCommand;

class Indexmaker extends JCommand
{
	/**
	 * An enabled flag.
	 *
	 * @var bool
	 */
	public static $isEnabled = true;

	public $name = 'index';

	public $description = 'Create empty index.html files in directories.';

	//        public $usage = 'example <command> [option]';

	public function configure()
	{
		parent::configure();
	}

	public function doExecute()
	{
		$path = isset($this->input->args[0]) ? JPATH_BASE . '/' . $this->input->args[0] : JPATH_BASE;

		$this->out('Scaning dirs...');

		$count = $this->createIndex($path);

		if ($count)
		{
			$this->out('OK! ' . $count . ' files created.');
		}
		else
		{
			$this->out('No file created.');
		}
	}

	protected function  createIndex($path)
	{
		// From: https://github.com/joomla/joomla-cms/blob/master/build/indexmaker.php
		$iterator  = new \RecursiveDirectoryIterator($path, \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::SKIP_DOTS);
		$flattened = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::SELF_FIRST);

		$count = 0;

		foreach ($flattened as $path => $dir)
		{
			if (!$dir->isDir())
			{
				continue;
			}

			// Add an index.html if neither an index.html nor an index.php exist
			if (!(file_exists($path . '/index.html') || file_exists($path . '/index.php')))
			{
				$this->out('File created: ' . $path . '/index.html');
				$count++;

				file_put_contents($path . '/index.html', '<!DOCTYPE html><title></title>' . "\n");
			}
		}

		return $count;
	}

}
