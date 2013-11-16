<?php

namespace Sqlsync\Table;

class VersionTable extends \JTable
{
	public function __construct(&$db)
	{
		return parent::__construct('sqlsync', 'id', $db);
	}
}