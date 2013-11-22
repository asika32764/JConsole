<?php
/**
 * OK
 */

namespace Sqlsync\Model;

use Joomla\Registry\Registry;
use Sqlsync\Exporter\AbstractExporter;
use Sqlsync\Helper\ProfileHelper;
use Sqlsync\Importer\AbstractImporter;
use Symfony\Component\Yaml\Dumper;

/**
 * Class Schema
 *
 * @package Sqlsync\Model
 */
class Schema extends \JModelDatabase
{
	protected $versionModel;

	public $schemaPath;

	public $backupPath;


	public function __construct()
	{
		parent::__construct();

		$this->schemaPath = ProfileHelper::getPath();

		$this->backupPath = ProfileHelper::getTmpPath() . '/backups';
	}

	/**
	 * Test
	 *
	 * @param  string $type         Test
	 * @param  bool   $ignoreTrack  wer
	 * @param  bool   $prefixOnly   erewr
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

	public function backup()
	{
		$database = new Database;

		$result = $database->save('sql', $this->backupPath);

		$this->state->set('dump.path', $database->getState()->get('dump.path'));

		return $result;
	}

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

	public function create($force = false, $type = 'yaml')
	{
		return $this->create($type);
	}

	public function import($force = false, $type = 'yaml')
	{
		$schema = file_get_contents($this->getPath($type));

		$importer = AbstractImporter::getInstance($type);

		$importer->import($schema);

		$this->state->set('import.analyze', $importer->getState()->get('import.analyze'));

		return true;
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
