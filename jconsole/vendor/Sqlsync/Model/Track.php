<?php

namespace Sqlsync\Model;

use Joomla\Registry\Registry;
use Sqlsync\Helper\ProfileHelper;
use Sqlsync\Helper\TableHelper;
use Symfony\Component\Yaml\Parser as SymfontYamlParser;

class Track extends \JModelBase
{
	protected $file;

	protected $global;

	public function __construct()
	{
		$this->global = SQLSYNC_LIB . '/Resource/track.yml';

		$this->file = ProfileHelper::getPath() . '/track.yml';
	}

	public function getTrackList()
	{
		$track = new Registry;

		if (file_exists($this->file))
		{
			$track->loadFile($this->global, 'yaml')
				->loadFile($this->file, 'yaml');
		}

		return $track;
	}

	public function setTrack($tables, $status = 'all')
	{
		$db = \JFactory::getDbo();

		$prefix = $db->getPrefix();

		$tables = (array) $tables;

		$track = $this->getTrackList();

		foreach ($tables as $table)
		{
			$table = TableHelper::stripPrefix($table, $prefix);

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

