<?php

namespace Sqlsync\Model;

use Sqlsync\Exporter\AbstractExporter;
use Sqlsync\Helper\ProfileHelper;

class Database extends \JModelBase
{
	public function export($type = 'sql')
	{
		/** @var $exporter AbstractExporter */
		$exporter = AbstractExporter::getInstance($type);
		$config   = \JFactory::getConfig();
		$profile  = ProfileHelper::getProfile();

		// Export it.
		$result = $exporter->export();

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

		\JFile::write($file, $result);

		return true;
	}
}