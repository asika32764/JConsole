<?php

namespace Sqlsync\Model\Profile;

use Sqlsync\Factory;

class ProfilesModel extends \JModelBase
{
	public function getItems()
	{
		$config = Factory::getConfig();

		$current = $config->get('profile', 'default');

		$profiles = new \FilesystemIterator(SQLSYNC_PROFILE, \FilesystemIterator::SKIP_DOTS);

		$items = array();

		foreach ($profiles as $profile)
		{
			if ($profile->isFile())
			{
				continue;
			}

			$item = new \Stdclass;

			$item->title = $profile->getBasename();
			$item->is_current = ($current == $item->title);
			$item->current_version = '';

			$items[] = $item;
		}

		return $items;
	}

	public function getList()
	{
		$profiles = new \FilesystemIterator(SQLSYNC_PROFILE, \FilesystemIterator::SKIP_DOTS);

		$items = array();

		foreach ($profiles as $profile)
		{
			if ($profile->isFile())
			{
				continue;
			}

			$items[] = $profile->getBasename();
		}

		return $items;
	}
}