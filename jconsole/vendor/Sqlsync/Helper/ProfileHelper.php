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

		return SQLSYNC_RESOURCE . '/profiles/' . $profile;
	}
}