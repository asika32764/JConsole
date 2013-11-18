<?php

namespace Sqlsync\Exporter;

abstract class AbstractExporter extends \JModelDatabase
{
	static protected $instance = array();

	static public function getInstance($type = 'sql')
	{
		if (!empty(self::$instance[$type]))
		{
			return self::$instance[$type];
		}

		$class = 'Sqlsync\\Exporter\\' . ucfirst($type) . 'Exporter';

		return self::$instance[$type] = new $class;
	}

	abstract public function export();

	abstract protected function getCreateTable($table);

	abstract protected function getInserts($table);
}