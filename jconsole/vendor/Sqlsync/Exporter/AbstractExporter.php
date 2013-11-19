<?php

namespace Sqlsync\Exporter;

use Sqlsync\Helper\AbstractQueryHelper;

abstract class AbstractExporter extends \JModelDatabase
{
	static protected $instance = array();

	protected $db;

	protected $queryHelper;

	static public function getInstance($type = 'sql')
	{
		if (!empty(self::$instance[$type]))
		{
			return self::$instance[$type];
		}

		$class = 'Sqlsync\\Exporter\\' . ucfirst($type) . 'Exporter';

		return self::$instance[$type] = new $class;
	}

	public function __construct()
	{
		parent::__construct();

		$this->queryHelper = AbstractQueryHelper::getInstance($this->db->name);
	}

	abstract public function export();

	abstract protected function getCreateTable($table);

	abstract protected function getInserts($table);
}