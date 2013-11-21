<?php

namespace Sqlsync\Importer;

use Joomla\Utilities\ArrayHelper;
use Sqlsync\Exporter\AbstractExporter;
use Symfony\Component\Yaml\Parser;

class YamlImporter extends AbstractImporter
{
	public $sql = array();

	protected $columns = array();

	public function import($content)
	{
		$parser = new Parser;

		$content = $parser->parse($content);

		// First level: Tables
		foreach ($content as $tableName => $table)
		{
			$newTableName = $this->renameTable($table);

			$tableName = $newTableName ?: $tableName;

			$this->changeColumns($tableName, ArrayHelper::getValue($table, 'columns', array()));
		}

		print_r($this->sql);
		die;
	}

	public function renameTable($table)
	{
		$from    = (array) ArrayHelper::getValue($table, 'from', array());
		$newName = ArrayHelper::getValue($table, 'name', array());

		$tableName = null;

		foreach ($from as $fromName)
		{
			$result = $this->db->setQuery($this->queryHelper->showColumns($fromName))->loadResult();

			if ($result && $newName != $fromName)
			{
				$tableName = $fromName;

				break;
			}
		}

		if ($tableName)
		{
			$this->sql[] = $sql = 'RENAME TABLE ' . $tableName . ' TO ' . $newName;

			// $this->db->setQuery($sql)->execute();

			return false;//$newName;
		}

		return false;
	}

	public function changeColumns($tableName, $columns)
	{
		$before = '';

		foreach ($columns as $columnName => $column)
		{
			$result = $this->renameColumn($tableName, $columnName, $column);

			if (!$result)
			{
				$result = $this->addColumn($tableName, $columnName, $before, $column);

				if ($result)
				{
					continue;
				}
			}

			$columnName = $result ?: $columnName;

			$this->changeColumn($tableName, $columnName, $column);

			$before = $columnName;
		}

		$this->dropColumns($tableName, $columns);
	}

	public function renameColumn($tableName, $columnName, $column)
	{
		$from    = (array) ArrayHelper::getValue($column, 'From', array());
		$newName = ArrayHelper::getValue($column, 'Field', array());

		$oldColumns = $this->getColumnList($tableName);

		$oldName = null;

		foreach ($oldColumns as $key => $val)
		{
			if (in_array($key, $from) && $newName != $key)
			{
				$oldName = $key;

				break;
			}
		}

		if ($oldName)
		{
			$this->sql[] = $sql = "ALTER TABLE {$tableName} CHANGE {$oldName} {$newName} {$column['Type']}";

			// $this->db->setQuery($sql)->execute();

			return false;// $newName;
		}

		return false;
	}

	protected function addColumn($tableName, $columnName, $before, $column)
	{
		$oldColumns = array_keys($this->getColumnList($tableName));

		if (!in_array($columnName, $oldColumns))
		{
			$null = ($column['Null'] == 'NO') ? 'NOT NULL' : '';

			@$ai = $column['Extra'] == 'auto_increment' ? 'AUTO_INCREMENT' : '';

			$comment = $column['Comment'] ? 'COMMENT ' . $this->db->quote($column['Comment']) : '';

			$position = $before ? 'AFTER ' . $before : 'FIRST';

			// Build sql
			$this->sql[] = $sql = "ALTER TABLE {$tableName} ADD {$columnName} {$column['Type']} {$null} {$ai} {$comment} {$position}";

			// $this->db->setQuery($sql)->execute();

			return true;
		}
	}

	protected function changeColumn($tableName, $columnName, $column)
	{
		$oldColumn = $this->getOldColumn($tableName, $columnName);

		unset($oldColumn['Collation']);
		unset($oldColumn['Key']);
		unset($oldColumn['Extra']);
		unset($oldColumn['Privileges']);
		unset($column['From']);


		if ($oldColumn == $column)
		{
			return false;
		}

		$null = ($column['Null'] == 'NO') ? 'NOT NULL' : '';

		@$ai = $column['Extra'] == 'auto_increment' ? 'AUTO_INCREMENT' : '';

		$comment = $column['Comment'] ? 'COMMENT ' . $this->db->quote($column['Comment']) : '';

		// Build sql
		$this->sql[] = $sql = "ALTER TABLE {$tableName} CHANGE {$columnName} {$columnName} {$column['Type']} {$null} {$ai} {$comment}";

		// $this->db->setQuery($sql)->execute();

		return true;

		// print_r($oldColumn);print_r($column);die;
	}

	protected function dropColumns($tableName, $columns)
	{
		$oldColumns = array_keys($this->getColumnList($tableName));

		$newColumns = array_keys($columns);

		foreach ($oldColumns as $column)
		{
			if (!in_array($column, $newColumns))
			{
				$this->sql[] = $sql = "ALTER TABLE {$tableName} DROP {$column}";

				// $this->db->setQuery($sql)->execute();
			}
		}
	}

	protected function getColumnList($table)
	{
		if (!empty($this->columns[$table]))
		{
			return $this->columns[$table];
		}

		$columns = $this->db->setQuery('SHOW FULL COLUMNS FROM ' . $this->db->quoteName($this->db->escape($table)))->loadAssocList('Field');

		return $this->columns[$table] = $columns;
	}

	protected function getOldColumn($tableName, $columnName)
	{
		$list = $this->getColumnList($tableName);

		return ArrayHelper::getValue($list, $columnName);
	}

}
