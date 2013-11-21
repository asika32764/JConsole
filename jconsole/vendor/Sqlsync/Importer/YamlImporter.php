<?php

namespace Sqlsync\Importer;

use Joomla\Utilities\ArrayHelper;
use Sqlsync\Exporter\AbstractExporter;
use Symfony\Component\Yaml\Parser;

class YamlImporter extends AbstractImporter
{
	public $sql = array();

	protected $columns = array();

	protected $indexes = array();

	protected $dataPks = array();

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

			$this->changeIndexes($tableName, ArrayHelper::getValue($table, 'index', array()));

			$this->changeDatas($tableName, ArrayHelper::getValue($table, 'data', array()));
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

			$this->execute($sql);

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

		return true;
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

			$this->execute($sql);

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

			$this->execute($sql);

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

		$this->execute($sql);

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

				$this->execute($sql);
			}
		}
	}

	protected function changeIndexes($tableName, $indexes)
	{
		$oldIndexes = $this->getOldIndexes($tableName);

		$oldIdxIdx = $this->getIndexesIndex($oldIndexes);

		$newIdxIdx = $this->getIndexesIndex($indexes);

		foreach ($newIdxIdx as $indexName => $columns)
		{
			$oldColumns = ArrayHelper::getValue($oldIdxIdx, $indexName);

			if ($oldColumns != $columns)
			{
				$this->changeIndex($tableName, $indexName, $columns, $indexes, (boolean) $oldColumns);
			}
		}

		$this->dropIndexes($tableName, $oldIdxIdx, $newIdxIdx);

		return true;
	}

	protected function changeIndex($tableName, $indexName, $columns,  $indexes, $noDrop = true)
	{
		$index = null;

		foreach ($indexes as $idx)
		{
			if ($idx['Key_name'] == $indexName)
			{
				$index = $idx;
			}
		}

		if ($noDrop)
		{
			$this->dropIndex($tableName, $indexName);
		}

		if ($index['Key_name'] == 'PRIMARY')
		{
			$this->sql[] = $sql = "ALTER TABLE {$tableName} ADD PRIMARY KEY (" . implode(', ', $columns) . ")";
		}
		else
		{
			$indexType = $index['Non_unique'] ? 'INDEX' : 'UNIQUE';

			$this->sql[] = $sql = "ALTER TABLE {$tableName} ADD {$indexType} `{$indexName}` (" . implode(', ', $columns) . ")";
		}



		$this->execute($sql);

		return true;
	}

	protected function dropIndexes($tableName, $oldIdxIdx, $newIdxIdx)
	{
		foreach ($oldIdxIdx as $oldIdx => $columns)
		{
			if (!isset($newIdxIdx[$oldIdx]))
			{
				$this->dropIndex($tableName, $oldIdx);
			}
		}

		return true;
	}

	protected function dropIndex($tableName, $indexName)
	{
		if ($indexName == 'PRIMARY')
		{
			$this->sql[] = $sql = "ALTER TABLE {$tableName} DROP PRIMARY KEY";
		}
		else
		{
			$this->sql[] = $sql = "ALTER TABLE {$tableName} DROP INDEX `{$indexName}`";
		}

		$this->execute($sql);

		return true;
	}

	protected function changeDatas($tableName, $datas)
	{
		if (!$datas)
		{
			return false;
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

	protected function getOldIndexes($table)
	{
		if (!empty($this->indexes[$table]))
		{
			return $this->indexes[$table];
		}

		$indexes = $this->db->setQuery("SHOW INDEX FROM `{$table}`")->loadAssocList();

		return $this->indexes[$table] = $indexes;
	}

	protected function getIndexesIndex($indexes)
	{
		$indexesIndex = array();

		foreach ($indexes as $index)
		{
			$keyname = $index['Key_name'];

			if (empty($indexesIndex[$keyname]))
			{
				$indexesIndex[$keyname] = array();
			}

			$indexesIndex[$keyname][] = $index['Column_name'];
		}

		return $indexesIndex;
	}

	protected function getOldDataPks($table)
	{
		if (!empty($this->dataPks[$table]))
		{
			return $this->dataPks[$table];
		}
	}

	protected function execute($sql)
	{
		// return $this->db->setQuery($sql)->execute();
	}
}
