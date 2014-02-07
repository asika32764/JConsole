<?php

namespace Command\Build\CheckPHP;

use JConsole\Command\JCommand;

class CheckPHP extends JCommand
{
	/**
	 * An enabled flag.
	 *
	 * @var bool
	 */
	public static $isEnabled = true;

	protected $name = 'check-constants';

	protected $description = 'Check php files which do not included Joomla constants.';

	protected $usage = 'check-constants <path>';

	protected $constants = array(
		'_JEXEC',
		'JPATH_PLATFORM',
		'JPATH_BASE',
		'AKEEBAENGINE',
		'WF_EDITOR'
	);

	protected $dump = false;


	public function configure()
	{
		$this->addOption(
			array('d', 'dump'),
			'/cli/jconsole/',
			'Dump to file, the log file will save in {ROOT}/logs.'
		);

		parent::configure();
	}

	public function doExecute()
	{
		jimport('joomla.log.log');

		if ($this->getOption('d'))
		{
			unlink(JPATH_BASE . '/logs/jconsole.missing.constants.php');

			\JLog::addLogger(
				array(
					//Sets file name
					'text_file' => 'jconsole.missing.constants.php'
				),
				//Sets all JLog messages to be set to the file
				\JLog::ALL,
				//Chooses a category name
				'jconsole'
			);

			$this->dump = true;
		}

		$path = isset($this->input->args[0]) ? JPATH_BASE . '/' . $this->input->args[0] : JPATH_BASE;

		$this->out('Scaning files...');

		$count = $this->checkFiles($path);

		$this->out();

		if (!$count)
		{
			$this->out('All files included constants.');
		}
		else
		{
			$this->out($count . ' files need constants.');

			if ($this->dump)
			{
				$this->out('Dumped log to ' . JPATH_BASE . '/logs/jconsole.missing.constants.php');
			}
		}
	}

	protected function  checkFiles($path)
	{
		// From: https://github.com/joomla/joomla-cms/blob/master/build/indexmaker.php
		$iterator  = new \RecursiveDirectoryIterator($path, \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::SKIP_DOTS);
		$flattened = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::SELF_FIRST);

		$count = 0;

		foreach ($flattened as $path => $dir)
		{
			if ($dir->isDir() || $dir->getExtension() != 'php')
			{
				continue;
			}

			$content = file($path);

			$hascode = $this->checkFile($content);

			if (!$hascode)
			{
				$count++;

				$this->out($path);

				if ($this->dump)
				{
					\JLog::add($path, \JLog::NOTICE, 'jconsole');
				}
			}
		}

		return $count;
	}

	protected function checkFile($content)
	{
		// Get the constants to look for
		$defines = $this->constants;

		$hascode = 0;

		foreach ($content AS $line)
		{
			$tline = trim($line);

			if ($tline == '' || $tline == '<?php' || $tline == '?>')
			{
				continue;
			}

			if ($tline['0'] != '/' && $tline['0'] != '*')
			{
				$hascode = 1;
			}

			// Search for "defined"
			$pos_1 = stripos($line, 'defined');

			// Skip the line if "defined" is not found
			if ($pos_1 === false)
			{
				continue;
			}

			// Search for "die".
			//  "or" may not be present depending on syntax
			$pos_3 = stripos($line, 'die');

			// Skip the line if "die" is not found
			if ($pos_3 === false)
			{
				continue;
			}

			// Search for the constant name
			foreach ($defines AS $define)
			{
				$define = trim($define);

				// Search for the define
				$pos_2 = strpos($line, $define);

				// Skip the line if the define is not found
				if ($pos_2 === false)
				{
					continue;
				}

				// Check the position of the words
				if ($pos_2 > $pos_1 && $pos_3 > $pos_2)
				{
					unset($content);

					return true;
				}
			}
		}

		unset($content);

		return $hascode ? false : true;
	}
}
