<?php

namespace Sqlsync\Helper;

abstract class AbstractQueryHelper
{
	static protected $instance = array();

	static public function getInstance($type = 'mysql')
	{
		if (!empty(self::$instance[$type]))
		{
			return self::$instance[$type];
		}

		$class = 'Sqlsync\\Helper\\QueryHelper\\' . ucfirst($type) . 'QueryHelper';

		return self::$instance[$type] = new $class;
	}

	public function __construct()
	{
		$this->db = \JFactory::getDbo();
	}

	abstract public function showCreateTable($table);

	abstract public function showColumns($table);

	abstract public function getAllData($table);

	abstract public function dropTable($table);
}