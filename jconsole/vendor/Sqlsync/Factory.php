<?php

namespace Sqlsync;

abstract class Factory
{
	static public $version = array();

	static public $schema;

	static public $config;

	public static function getVersion($profile = null, $db = null)
	{
		if (isset(self::$version[$profile]))
		{
			return self::$version[$profile];
		}

		return self::$version[$profile] = new Version($profile, $db);
	}

	public static function getSchema($db = null)
	{
		if (self::$schema)
		{
			return self::$schema;
		}

		return self::$schema = new Schema($db);
	}

	public static function getConfig()
	{
		if (self::$config)
		{
			return self::$config;
		}

		$defaultConfig = __DIR__ . '/Resource/config.yml';

		$userConfig = SQLSYNC_RESOURCE . '/config.yml';

		if (!file_exists($userConfig))
		{
			file_put_contents($userConfig, '');
		}

		$config = new Config;

		$config->loadFile($defaultConfig, 'yaml')
			->loadFile($userConfig, 'yaml');

		return self::$config = $config;
	}
}