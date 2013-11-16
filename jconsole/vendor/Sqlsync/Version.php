<?php

namespace Sqlsync;

use Sqlsync\Table\VersionTable;

class Version
{
	protected $db;

	protected $profile = 'main';

	protected $current;

	public function __construct($profile = 'main', \JDatabaseDriver $db = null)
	{
		$this->db = $db ?: \JFactory::getDbo();
		$this->profile = $profile ?: 'main';
	}

	public function getCurrent()
	{
		if ($this->current)
		{
			return $this->current;
		}

		if (!$this->hasTable($this->profile))
		{
			$this->createTable($this->profile);
		}

		$query = $this->db->getQuery(true);

		$query->select('version')
			->from('sqlsync')
			->where("profile = '{$this->profile}'");

		$version = $this->db->setQuery($query)->loadResult();

		if (!$version)
		{
			$version = $this->addNew();
		}

		return $this->current = $version;
	}

	public function addNew()
	{
		if (!$this->hasTable())
		{
			$this->createTable();
		}

		$current = $this->current = $this->generateVersion();

		$versionTable = new VersionTable($this->db);

		$versionTable->load(array('profile' => $this->profile));

		$versionTable->profile = $this->profile;

		$versionTable->version = $this->generateVersion();

		$versionTable->store();

		return $current;
	}

	public function hasTable()
	{
		return (boolean) $sqlsyncTable = $this->db->setQuery('SHOW TABLES LIKE "sqlsync"')->loadResult();
	}

	public function createTable()
	{
		$sql = "CREATE TABLE IF NOT EXISTS `sqlsync` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`profile` varchar(255) NOT NULL,
				`version` varchar(255) NOT NULL,
				PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

		$this->db->setQuery($sql)->execute();
	}

	public function generateVersion()
	{
		return time();
	}
}