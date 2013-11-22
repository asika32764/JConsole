<?php

namespace Sqlsync\Helper;

use Sqlsync\Factory;

abstract class ProfileHelper
{
	static public function getProfile()
	{
		$config = Factory::getConfig();

		return $config->get('profile', 'default');
	}

	static public function getPath()
	{
		$profile = self::getProfile();

		return SQLSYNC_RESOURCE . '/' . $profile;
	}

	static public function getTmpPath()
	{
		$profile = self::getProfile();

		return JPATH_ROOT . '/tmp/sqlsync/' . $profile;
	}
}
