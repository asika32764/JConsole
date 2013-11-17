<?php

namespace Sqlsync\Track;

use Joomla\Registry\Registry;
use Symfony\Component\Yaml\Parser as SymfontYamlParser;

class Track extends \JModelBase
{
	protected $file;

	public function __construct()
	{
		$this->file = JPATH_CLI . '/jconsole/resource/sql/track.yml';
	}

	public function getTrackList()
	{
		$track = new Registry;

		if (file_exists($this->file))
		{
			$track->loadFile($this->file, 'yaml');
		}

		return $track;
	}

	public function setTrack($tables, $status = 'all')
	{
		$db = \JFactory::getDbo();

		$tables = (array) $tables;

		$track = $this->getTrackList();

		foreach ($tables as $table)
		{
			$table = $db->replacePrefix($table);

			$track->set('table.' . $table, $status);
		}

		$this->saveTrackList($track);
	}

	public function saveTrackList($track)
	{
		jimport('joomla.filesystem.file');

		$content = $track->toString('yaml');

		\JFile::write($this->file, $content);
	}

	protected function stripPrefix($table)
	{
		$db = \JFactory::getDbo();

		$prefix = $db->getPrefix();

		$num = strlen($prefix);

		$table = '#__' . substr($table, $num);

		return $table;
	}
}

