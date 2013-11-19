<?php

namespace Sqlsync\Model;

use Sqlsync\Helper\TableHelper;

class Column extends \JModelDatabase
{
	protected $schema;

	public function __construct()
	{
		parent::__construct();
	}

	public function getColumnSchema($table, $column)
	{
		$schemaModel = new Schema;

		$this->schema = $schema = $schemaModel->getCurrent();

		$column = $schema->get($table . '.columns.' . $column);

		return $column;
	}
}
