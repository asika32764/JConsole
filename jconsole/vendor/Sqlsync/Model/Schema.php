<?php

namespace Sqlsync\Model;

use Joomla\Registry\Registry;
use Sqlsync\Exporter\AbstractExporter;
use Sqlsync\Helper\ProfileHelper;

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

		$path    = $this->initPath;

		if (file_exists($path))
		{
			$msg = "Already initialised.\nFile in: " . $path;

			throw new \RuntimeException($msg);
		}

		\JFile::write($path, $content);

		$state   = $this->getState();

		// $state->set('dump.version.new', $version);

		$state->set('dump.path', $path);

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

		$path    = $this->getCurrentPath($version);

		\JFile::write($path, $content);

		$state   = $this->getState();

		$state->set('dump.version.new', $version);

		$state->set('dump.path', $path);

		return true;
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
}
