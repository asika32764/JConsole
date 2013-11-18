<?php

namespace Sqlsync\Exporter;

use Sqlsync\Table\Table;
use Sqlsync\Track\Track;

class SqlExporter extends AbstractExporter
{
	public function export()
	{
		$tableObject = new Table;
		$trackObject = new Track;
		$tables      = $tableObject->listAll();
		$track       = $trackObject->getTrackList();

		$sql = array();

		foreach ($tables as $table)
		{
			$trackStatus = $track->get('table.' . $table, 'none');

			if ($trackStatus == 'none')
			{
				continue;
			}

			$sql[] = "DROP TABLE IF EXISTS `{$table}`";
			$sql[] = $this->getCreateTable($table);

			if ($trackStatus == 'all')
			{
				$insert = $this->getInserts($table);

				if ($insert)
				{
					$sql[] = $insert;
				}
			}
		}

		return implode(";\n\n", $sql) . ';';
	}

	protected function getCreateTable($table)
	{
		$db = $this->db;

		$result = $db->setQuery('SHOW CREATE TABLE ' . $db->quoteName($db->escape($table)))->loadRow();

		$sql = preg_replace('#AUTO_INCREMENT=\S+#is', '', $result[1]);

		return $sql;
	}

	protected function getInserts($table)
	{
		$db      = $this->db;
		$query   = $db->getQuery(true);
		$columns = $db->setQuery("SHOW COLUMNS FROM `{$table}`")->loadColumn();
		$datas   = $db->setQuery("SELECT * FROM `{$table}`")->getIterator('ArrayObject');

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

		$query->insert($table)->columns(implode(', ', $columns));

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

			$query->values(implode(', ', $data));
		}

		return (string) $query;
	}
}