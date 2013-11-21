<?php

namespace Sqlsync\Exporter;

use Sqlsync\Helper\TableHelper;
use Sqlsync\Model\Table;
use Sqlsync\Model\Track;

class SqlExporter extends AbstractExporter
{
	public function export($ignoreTrack = false, $prefixOnly = false)
	{
		$tableObject = new Table;
		$trackObject = new Track;
		$tables      = $prefixOnly ? $tableObject->listSite() : $tableObject->listAll();
		$track       = $trackObject->getTrackList();

		$sql = array();

		$this->tableCount = 0;
		$this->rowCount   = 0;

		foreach ($tables as $table)
		{
			$trackStatus = $track->get('table.' . $table, 'none');

			if ($trackStatus == 'none' && !$ignoreTrack)
			{
				continue;
			}

			$sql[] = $this->queryHelper->dropTable($table);
			$sql[] = $this->getCreateTable($table);

			$this->tableCount++;

			if ($trackStatus == 'all' || $ignoreTrack)
			{
				$insert = $this->getInserts($table);

				if ($insert)
				{
					$sql[] = $insert;
				}
			}
		}

		$this->state->set('dump.count.tables', $this->tableCount);
		$this->state->set('dump.count.rows', $this->rowCount);

		return implode(";\n\n", $sql) . ';';
	}

	protected function getCreateTable($table)
	{
		$db = $this->db;

		$result = $db->setQuery($this->queryHelper->showCreateTable($table))->loadRow();

		$sql = preg_replace('#AUTO_INCREMENT=\S+#is', '', $result[1]);

		$sql = explode("\n", $sql);

		$tableStriped = TableHelper::stripPrefix($result[0], $db->getPrefix());

		$sql[0] = str_replace($result[0], $tableStriped, $sql[0]);

		$sql = implode("\n", $sql);

		return $sql;
	}

	protected function getInserts($table)
	{
		$db      = $this->db;
		$query   = $db->getQuery(true);
		$columns = $db->setQuery($this->queryHelper->showColumns($table))->loadColumn();
		$datas   = $db->setQuery($this->queryHelper->getAllData($table))->getIterator('ArrayObject');

		if (!count($datas))
		{
			return null;
		}

		$columns = array_map(
			function($t) use ($query)
			{
				return $query->qn($t);
			},
			$columns
		);

		/** @var $query \JDatabaseQueryMysqli */
		$query->insert($query->quoteName($query->escape($table)));

		$columns = ' (' . implode(', ', $columns) . ')';

		$values = array();

		foreach ($datas as $data)
		{
			$data = (array) $data;

			$data = array_map(
				function($d) use ($query)
				{
					return $query->q($d);
				},
				$data
			);

			$values[] = implode(', ', $data);

			$this->rowCount++;
		}

		$query = $query . $columns . $values = new \JDatabaseQueryElement("VALUES ()", $values, ")," . PHP_EOL . "(");

		return (string) $query;
	}
}