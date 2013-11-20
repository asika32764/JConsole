<?php

namespace Sqlsync\Model;

use Joomla\Registry\Registry;
use Sqlsync\Exporter\AbstractExporter;
use Sqlsync\Helper\ProfileHelper;
use Symfony\Component\Yaml\Dumper;

class Schema extends \JModelDatabase
{
	protected $versionModel;

	public $initPath;

	public function __construct()
	{
		parent::__construct();

		$this->initPath = ProfileHelper::getPath() . '/schema/init.yml';
	}

	public function init()
	{
		$content = $this->export(false, false);

		$this->saveInit($content);

		return true;
	}

	public function create($force = false)
	{
		$version = $this->getCurrentVersion();

		$list = $this->listAllVersion();

		if (in_array($version, $list) && !$force)
		{
			throw new \RuntimeException('Now is newest version: ' . $version);
		}

		$versionModel = $this->getVersionModel();

		$versionModel->addNew();

		$version = $this->getCurrentVersion();

		$content = $this->export(false, false);

		$this->saveVersion($version, $content);

		$this->state->set('dump.version.new', $version);

		return true;
	}

	public function save($path, $content)
	{
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

	public function saveInit($content)
	{
		if (file_exists($this->initPath))
		{
			$msg = "Already initialised.\nFile in: " . $this->initPath;

			throw new \RuntimeException($msg);
		}

		return $this->save($this->initPath, $content);
	}

	public function saveVersion($version, $content)
	{
		$version = $version ?: $this->getCurrentVersion();

		$path = $this->getVersionPath($version);

		return $this->save($path, $content);
	}

	public function hasInit()
	{
		$path    = $this->initPath;

		if (file_exists($path))
		{
			return true;
		}

		return false;
	}

	public function export($ignoreTrack = false, $onlyPrefix = false)
	{
		$expoter = AbstractExporter::getInstance('yaml');

		/** @var $expoter AbstractExporter */
		$result = $expoter->export($ignoreTrack, $onlyPrefix);

		$this->state->set('dump.count.tables', $expoter->getState()->get('dump.count.tables'));

		$this->state->set('dump.count.rows', $expoter->getState()->get('dump.count.rows'));

		return $result;
	}


	public function getCurrentVersion()
	{
		$version = $this->getVersionModel();

		return $version->getCurrent();
	}

	public function listAllVersion()
	{
		$version = $this->getVersionModel();

		return $version->listAll();
	}

	/**
	 * @return mixed
	 */
	public function getVersionModel()
	{
		if ($this->versionModel)
		{
			return $this->versionModel;
		}

		return $this->versionModel = new Version;
	}

	public function getCurrent()
	{
		$version = $this->getCurrentVersion();

		return $this->loadSchema($version);
	}

	public function loadSchema($version)
	{
		$path = $this->getVersionPath($version);

		$schema = new Registry;

		$schema->loadFile($path, 'yaml');

		return $schema;
	}

	public function getPath()
	{
		return ProfileHelper::getPath() . '/schema';
	}

	public function getVersionPath($version = null)
	{
		return $this->getPath() . '/' . $version . '/schema.yml';
	}

	public function getCurrentPath()
	{
		$version = $this->getCurrentVersion();

		return $this->getVersionPath($version);
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
