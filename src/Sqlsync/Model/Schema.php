<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sqlsync\Model;

use Joomla\Registry\Registry;
use Sqlsync\Exporter\AbstractExporter;
use Sqlsync\Helper\ProfileHelper;
use Sqlsync\Importer\AbstractImporter;
use Symfony\Component\Yaml\Dumper;

/**
 * Class Schema
 */
class Schema extends \JModelDatabase
{
	/**
	 * @var string
	 */
	public $schemaPath;

	/**
	 * @var string
	 */
	public $backupPath;

	/**
	 * Constructor
 	 */
	public function __construct()
	{
		parent::__construct();

		$this->schemaPath = ProfileHelper::getPath();

		$this->backupPath = ProfileHelper::getTmpPath() . '/backups';
	}

	/**
	 * export
	 *
	 * @param string $type
	 * @param bool   $ignoreTrack
	 * @param bool   $prefixOnly
	 *
	 * @return bool
	 */
	public function export($type = 'yaml', $ignoreTrack = false, $prefixOnly = false)
	{
		$expoter = AbstractExporter::getInstance($type);

		/** @var $expoter AbstractExporter */
		$content = $expoter->export($ignoreTrack, $prefixOnly);

		$result = $this->save($this->getPath($type), $content);

		$this->state->set('dump.count.tables', $expoter->getState()->get('dump.count.tables'));

		$this->state->set('dump.count.rows', $expoter->getState()->get('dump.count.rows'));

		return $result;
	}

	/**
	 * save
	 *
	 * @param null $path
	 * @param null $content
	 *
	 * @return bool
	 * @throws \RuntimeException
	 */
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

	/**
	 * backup
	 *
	 * @return bool
	 */
	public function backup()
	{
		$database = new Database;

		$result = $database->save('sql', $this->backupPath);

		$this->state->set('dump.path', $database->getState()->get('dump.path'));

		return $result;
	}

	/**
	 * restore
	 *
	 * @return bool
	 * @throws \RuntimeException
	 */
	public function restore()
	{
		$model = new Database;

		$backups = \JFolder::files($this->backupPath, '.', false, true);

		rsort($backups);

		if (empty($backups[0]) || !file_exists($backups[0]))
		{
			throw new \RuntimeException('No backup file, please backup first.');
		}

		$content = file_get_contents($backups[0]);

		$model->dropAllTables();

		$model->import($content);

		$this->state->set('import.queries', $model->getState()->get('import.queries'));

		return true;
	}

	/**
	 * create
	 *
	 * @param bool   $force
	 * @param string $type
	 *
	 * @return mixed
	 */
	public function create($force = false, $type = 'yaml')
	{
		return $this->create($type);
	}

	/**
	 * import
	 *
	 * @param bool   $force
	 * @param string $type
	 *
	 * @return bool
	 */
	public function import($force = false, $type = 'yaml')
	{
		$schema = file_get_contents($this->getPath($type));

		$importer = AbstractImporter::getInstance($type);

		$importer->import($schema);

		$this->state->set('import.analyze', $importer->getState()->get('import.analyze'));

		return true;
	}

	/**
	 * load
	 *
	 * @param string $type
	 *
	 * @return Registry
	 */
	public function load($type = 'yaml')
	{
		$schema = new Registry;

		$schema->loadFile($this->getPath($type), $type);

		return $schema;
	}

	/**
	 * getPath
	 *
	 * @param string $type
	 *
	 * @return string
	 */
	public function getPath($type = 'yaml')
	{
		$ext = ($type == 'yaml') ? 'yml' : $type;

		return ProfileHelper::getPath() . '/schema.' . $ext;
	}

	/**
	 * objectToArray
	 *
	 * @param $d
	 *
	 * @return array
	 */
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
