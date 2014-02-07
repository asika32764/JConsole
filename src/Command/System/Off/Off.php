<?php
/**
 * Created by PhpStorm.
 * User: Ezio
 * Date: 2013/11/14
 * Time: 下午 9:02
 */

namespace Command\System\Off;

use Command\System\On\On;

class Off extends On
{
	/**
	 * An enabled flag.
	 *
	 * @var bool
	 */
	public static $isEnabled = true;

	protected $name = 'off';

	protected $description = 'Set this site offline.';

	protected $usage = 'off [option]';

	protected $offline = 1;
}
