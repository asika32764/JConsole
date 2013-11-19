<?php

namespace Sqlsync\Model;

use Sqlsync\Helper\TableHelper;

class Table extends \JModelDatabase
{
	public $prefix;

	public function __construct()
	{
		parent::__construct();

		$this->prefix = $this->db->getPrefix();
	}

	public function listAll()
	{
		return $this->listTables();
	}

	public function listSite()
	{
		return $this->listTables($this->prefix);
	}

	public function listTables($like = '')
	{
		$db = \JFactory::getDbo();

		// Show list tables
		$sql = 'SHOW TABLES';

		if ($like)
		{
			$sql .= " LIKE '{$like}%'";
		}

		$tables = $db->setQuery($sql)->loadColumn();

		foreach ($tables as &$table)
		{
			$table = $this->stripPrefix($table);
		}

		return $tables;
	}

	public function status()
	{
		$trackObject = new Track;

		$tables = $this->listAll();

		$track  = $trackObject->getTrackList();

		$statusList = array();

		foreach ($tables as $table)
		{
			$status = array();

			$trackStatus = $track->get('table.' . $table);

			$status['table']  = $table;

			$status['status'] = $trackStatus ?: 'none';

			$statusList[] = $status;
		}

		return $statusList;
	}

	protected function stripPrefix($table)
	{
		return TableHelper::stripPrefix($table, $this->prefix);
	}
}