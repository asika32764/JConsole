<?php

namespace Sqlsync\Model;

use Sqlsync\Exporter\AbstractExporter;
use Sqlsync\Helper\ProfileHelper;

class Database extends \JModelDatabase
{
	public function save($type = 'sql')
	{
		$config   = \JFactory::getConfig();
		$profile  = ProfileHelper::getProfile();

		$export = $this->export($type);

		$file = 'site-' . $config->get('db') . '-' . $profile . '-' . date('Y-m-d-H-i-s');

		if ($type == 'yaml')
		{
			$file .= '.yml';
		}
		else
		{
			$file .= '.' . $type;
		}

		$file = ProfileHelper::getPath() . '/export/' . $type . '/' . $file;

		\JFile::write($file, $export);

		return true;
	}

	public function export($type = 'sql')
	{
		/** @var $exporter AbstractExporter */
		$exporter = AbstractExporter::getInstance($type);

		// Export it.
		return $exporter->export();
	}

	public function getExported()
	{
		$path = ProfileHelper::getPath();

		$list = \JFolder::files($path . '/export/sql', '.', false, true);

		rsort($list);

		return $list;
	}

	public function importFromFile($file)
	{
		$sql = file_get_contents($file);

		$sql = trim($sql);

		return $this->import($sql);
	}

	public function import($queries)
	{
		if (!is_array($queries))
		{
			$queries = $this->db->splitSql($queries);
		}

		foreach ($queries as $query)
		{
			$query = trim($query);

			$this->db->setQuery($query)->execute();
		}

		$this->state->set('import.queries', count($queries));

		return true;
	}

	public function dropAllTables()
	{
		$tables = $this->db->setQuery('SHOW TABLES')->loadColumn();

		if (!$tables)
		{
			return;
		}

		$this->db->setQuery('DROP TABLE IF EXISTS ' . implode(', ', $tables))->execute();

		return true;
	}
}
