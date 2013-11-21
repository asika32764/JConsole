<?php

namespace Sqlsync\Exporter;

use Sqlsync\Model\Table;
use Sqlsync\Model\Track;
use Symfony\Component\Yaml\Dumper;
use VerbalExpressions\PHPVerbalExpressions\VerbalExpressions;

class YamlExporter extends AbstractExporter
{
	public function export($ignoreTrack = false, $prefixOnly = false)
	{
		$tableObject = new Table;
		$trackObject = new Track;
		$tables      = $prefixOnly ? $tableObject->listSite() : $tableObject->listAll();
		$track       = $trackObject->getTrackList();

		$result = array();

		$this->tableCount = 0;
		$this->rowCount   = 0;

		foreach ($tables as $table)
		{
			$trackStatus = $track->get('table.' . $table, 'none');

			if ($trackStatus == 'none' && !$ignoreTrack)
			{
				continue;
			}

			$result[$table] = $this->getCreateTable($table);

			$this->tableCount++;

			if ($trackStatus == 'all' || $ignoreTrack)
			{
				$insert = $this->getInserts($table);

				if ($insert)
				{
					$result[$table] = array_merge($result[$table], $insert);
				}
			}
		}

		$this->state->set('dump.count.tables', $this->tableCount);
		$this->state->set('dump.count.rows', $this->rowCount);

		$dumper = new Dumper;

		return $dumper->dump(json_decode(json_encode($result), true), 3, 0, false, true);
	}

	protected function getCreateTable($table)
	{
		$db = $this->db;

		$columns = $db->setQuery('SHOW FULL COLUMNS FROM ' . $db->quoteName($db->escape($table)))->loadAssocList('Field');
		$indexes = $db->setQuery("SHOW INDEX FROM `{$table}`")->loadAssocList();

		// Handle column details
		foreach ($columns as &$column)
		{
			// Unsigned
			if (strpos($column['Type'], 'unsigned'))
			{
				$column['Unsigned'] = true;
				$column['Type'] = substr($column['Type'], 0, -9);
			}
			else
			{
				$column['Unsigned'] = false;
			}

			/*
			// Type and length
			$match = array();

			if (preg_match('/^(.*)\((.*)\)$/', $column['Type'], $match))
			{
				$column['Type']   = $match[1];
				$column['Length'] = $match[2];
			}
			*/

			$column['From'] = array($column['Field']);

			unset($column['Key']);
			unset($column['Privileges']);
			unset($column['Extra']);
			unset($column['Collation']);
		}

		// Handle index details
		foreach ($indexes as &$index)
		{
			unset($index['Model']);
			unset($index['Collation']);
			unset($index['Cardinality']);
			unset($index['Sub_part']);
			unset($index['Packed']);
			unset($index['Index_type']);
		}

		$result['name'] = $table;
		$result['from'] = array($table);
		$result['columns'] = $columns;
		$result['index'] = $indexes;
		// $sql = preg_replace('#AUTO_INCREMENT=\S+#is', '', $result[1]);

		return $result;
	}

	protected function getInserts($table)
	{
		$db      = $this->db;
		$query   = $db->getQuery(true);
		//$columns = $db->setQuery("SHOW COLUMNS FROM `{$table}`")->loadColumn();
		$datas   = $db->setQuery("SELECT * FROM `{$table}`")->loadRowList();

		if (!count($datas))
		{
			return null;
		}

		//$result['columns'] = $columns;

		$result['data'] = $datas;

		$this->rowCount += count($datas);

		return $result;
	}
}
