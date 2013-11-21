<?php

namespace Sqlsync\Importer;

use Sqlsync\Helper\AbstractQueryHelper;

abstract class AbstractImporter extends \JModelDatabase
{
	static protected $instance = array();

	/**
	 * @var AbstractQueryHelper
	 */
	protected $queryHelper;

	protected $tableCount = 0;

	protected $rowCount = 0;

	static public function getInstance($type = 'yaml')
	{
		if (!empty(self::$instance[$type]))
		{
			return self::$instance[$type];
		}

		$class = 'Sqlsync\\Importer\\' . ucfirst($type) . 'Importer';

		return self::$instance[$type] = new $class;
	}

	public function __construct()
	{
		parent::__construct();

		$this->queryHelper = AbstractQueryHelper::getInstance($this->db->name);
	}

	abstract public function import($content);
}