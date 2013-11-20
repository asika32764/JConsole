<?php

namespace Sqlsync\Helper;

abstract class TypeValidator
{
	public static function validate($type)
	{
		if (!$type)
		{
			return true;
		}

		return true;
	}
}
