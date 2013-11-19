<?php

namespace Sqlsync\Helper\QueryHelper;

use Sqlsync\Helper\AbstractQueryHelper;

class MysqliQueryHelper extends AbstractQueryHelper
{
	public function showCreateTable($table)
	{
		return 'SHOW CREATE TABLE ' . $this->db->quoteName($this->db->escape($table));
	}

	public function showColumns($table)
	{
		return 'SHOW FULL COLUMNS FROM ' . $this->db->quoteName($this->db->escape($table));
	}

	public function getAllData($table)
	{
		$query = $this->db->getQuery(true);

		return $query->select('*')->from($query->quoteName($table));
	}

	public function dropTable($table)
	{
		return "DROP TABLE IF EXISTS `{$table}`";
	}
}