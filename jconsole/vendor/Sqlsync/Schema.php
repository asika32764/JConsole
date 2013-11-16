<?php

namespace Sqlsync;

use Command\Sql\Helper\JsonHelper;

class Schema extends \JModelBase
{
	protected $db;

	protected $path;

	public function __construct(\JDatabaseDriver $db = null)
	{
		parent::__construct();

		$this->db = $db ?: \JFactory::getDbo();

		$this->path = JPATH_CLI . '/jconsole/resource/sql/schema/';
	}

	public function getCurrentVersion($profile = 'main')
	{
		$version = Factory::getVersion($profile);

		return $version->getCurrent();
	}

	public function dump($version = null, $path = null)
	{
		jimport('joomla.filesystem.file');

		$config  = \JFactory::getConfig();
		$db      = $this->db;
		$prefix  = $config->get('dbprefix');
		$version = $version ?: Factory::getVersion()->addNew();
		$tables  = $db->setQuery("SHOW TABLES LIKE '{$prefix}%'")->loadColumn();
		$columns = array();

		foreach ($tables as $table)
		{
			$columns[$table]['schema'] = $db->setQuery("SHOW FULL COLUMNS FROM `{$table}`")->loadAssocList('Field');
			$columns[$table]['index']  = $db->setQuery("SHOW INDEX FROM `{$table}`")->loadAssocList();
		}

		$path = $path
			? JPATH_CLI . '/jconsole/resource/' . $path
			: $this->path . $version;

		/*
		$dumper = new SymfonyYamlDumper;

		foreach ($columns as $table => $column)
		{
			//$content = $dumper->dump(json_decode(json_encode($column), true), 2, 0, false, true);

			//\JFile::write($file . $table . '.yml', $content);
		}
		*/

		$content = JsonHelper::encode($columns);
		$path    = $path . "/schema.json";
		$state   = $this->getState();

		\JFile::write($path, $content);

		$state->set('dump.version.new', $version);

		$state->set('dump.path', $path);

		$state->set('dump.count.tables', count($tables));

		return true;
	}

	public function listAllVersion()
	{
		$dirs = new \DirectoryIterator($this->path);

		$list = array();

		foreach ($dirs as $dir)
		{
			if ($dir->isFile() || $dir->getBasename() == 'main' || $dir->isDot())
			{
				continue;
			}

			$list[] = $dir->getBasename();
		}

		return $list;
	}
}