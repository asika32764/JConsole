<?php

namespace Sqlsync\Helper;

abstract class TableHelper
{
	static public function stripPrefix($table, $prefix = null)
	{
		$prefix = $prefix ?: \JFactory::getDbo()->getPrefix();

		$num = strlen($prefix);

		if (substr($table, 0, $num) == $prefix)
		{
			$table = '#__' . substr($table, $num);
		}

		return $table;
	}
}