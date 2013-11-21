<?php

namespace Sqlsync\Model;

use Joomla\Registry\Registry;
use Sqlsync\Exporter\AbstractExporter;
use Sqlsync\Helper\ProfileHelper;
use Symfony\Component\Yaml\Dumper;

class Schema extends \JModelDatabase
{
	protected $versionModel;

	public $schemaPath;

	public function __construct()
	{
		parent::__construct();

		$this->schemaPath = ProfileHelper::getPath();
	}

	public function export($type = 'yaml')
	{
		$expoter = AbstractExporter::getInstance($type);

		/** @var $expoter AbstractExporter */
		$content = $expoter->export(false, false);

		$result = $this->save($this->getPath($type), $content);

		$this->state->set('dump.count.tables', $expoter->getState()->get('dump.count.tables'));

		$this->state->set('dump.count.rows', $expoter->getState()->get('dump.count.rows'));

		return $result;
	}

	public function save($path = null, $content = null)
	{
		$path = $path ?: $this->getPath('yaml');

		if ($content instanceof Registry)
		{
			$content = $content->toArray();

			$dumper = new Dumper;

			$content = $dumper->dump($content, 3, 0);
		}

		if (!\JFile::write($path, $content))
		{
			throw new \RuntimeException(sprintf('Save schema "%" fail.', $path));
		}

		$this->state->set('dump.path', $path);

		return true;
	}

	public function create($force = false, $type = 'yaml')
	{
		return $this->create($type);
	}

	public function load($type = 'yaml')
	{
		$schema = new Registry;

		$schema->loadFile($this->getPath($type), $type);

		return $schema;
	}

	public function getPath($type = 'yaml')
	{
		$ext = ($type == 'yaml') ? 'yml' : $type;

		return $this->schemaPath . '/schema.' . $ext;
	}

	public function objectToArray($d)
	{
		if (is_object($d))
		{
			$d = get_object_vars($d);
		}

		if (is_array($d))
		{
			return array_map(array($this, __FUNCTION__), $d);
		}
		else
		{
			// Return array
			return $d;
		}
	}
}
