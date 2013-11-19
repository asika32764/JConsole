<?php

namespace Sqlsync\Exporter;

use Sqlsync\Table\Table;
use Sqlsync\Track\Track;
use Symfony\Component\Yaml\Dumper;

class YamlExporter extends AbstractExporter
{
	protected $db;

	public function __construct()
	{
		$this->db = \JFactory::getDbo();
	}

	public function export()
	{
		$tableObject = new Table;
		$trackObject = new Track;
		$tables      = $tableObject->listAll();
		$track       = $trackObject->getTrackList();

		$result = array();

		foreach ($tables as $table)
		{
			$trackStatus = $track->get('table.' . $table, 'none');

			if ($trackStatus == 'none')
			{
				continue;
			}

			$result[$table] = $this->getCreateTable($table);

			if ($trackStatus == 'all')
			{
				$insert = $this->getInserts($table);

				if ($insert)
				{
					$result[$table] = array_merge($result[$table], $insert);
				}
			}
		}

		$dumper = new Dumper;

		return $dumper->dump(json_decode(json_encode($result), true), 3, 0, false, true);
	}

	protected function getCreateTable($table)
	{
		$db = $this->db;

		$result['columns'] = $db->setQuery('SHOW FULL COLUMNS FROM ' . $db->quoteName($db->escape($table)))->loadAssocList('Field');
		$indexes   = $db->setQuery("SHOW INDEX FROM `{$table}`")->loadAssocList();

		foreach ($indexes as &$index)
		{
			unset($index['Table']);
			unset($index['Collation']);
			unset($index['Cardinality']);
			unset($index['Sub_part']);
			unset($index['Packed']);
			unset($index['Index_type']);
		}

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

		return $result;
	}
}
