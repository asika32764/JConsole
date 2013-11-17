<?php

namespace Sqlsync;

abstract class Factory
{
	static public $version = array();

	static public $schema;

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

	}
}