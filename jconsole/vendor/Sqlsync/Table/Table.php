<?php

namespace Sqlsync\Table;

class Table extends \JModelBase
{
	public function listAll()
	{
		return $this->listTables();
	}

	public function listSite()
	{
		$db = \JFactory::getDbo();

		$prefix = $db->getPrefix();

		return $this->listTables($prefix);
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

		return $db->setQuery($sql)->loadColumn();
	}
}