<?php

namespace Sqlsync\Helper;

abstract class JsonHelper
{
	public static function encode($data, $option = null)
	{
		if (version_compare(PHP_VERSION, '5.4', '>'))
		{
			$option = $option | JSON_PRETTY_PRINT;
		}

		return json_encode($data, $option);
	}
}