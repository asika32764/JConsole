<?php

namespace Sqlsync\Model;

use Sqlsync\Exporter\AbstractExporter;
use Sqlsync\Helper\ProfileHelper;

class Database extends \JModelBase
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
}