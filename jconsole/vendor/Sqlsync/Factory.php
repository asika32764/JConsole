<?php

namespace Sqlsync;

abstract class Factory
{
	static public $config;

	public static function getConfig()
	{
		if (self::$config)
		{
			return self::$config;
		}

		$defaultConfig = __DIR__ . '/Resource/config.yml';

		$userConfig = JPATH_ROOT . '/tmp/sqlsync/config.yml';

		if (!file_exists($userConfig))
		{
			$content = '';

			\JFile::write($userConfig, $content);
		}

		$config = new Config;

		$config->loadFile($defaultConfig, 'yaml')
			->loadFile($userConfig, 'yaml');

		return self::$config = $config;
	}
}