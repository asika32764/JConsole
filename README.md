# JConsole
#### An useful console tools for Joomla CMS

JConsole is a cli tools implement from [Joomla Console Package](https://github.com/asika32764/joomla-framework/tree/console/src/Joomla/Console). It provides an interface to create nested commands.

About Console package:

Please see: https://github.com/asika32764/joomla-framework/tree/console/src/Joomla/Console

> Note: Console package has not added to Joomla Framework yet, it is an experimental package.

## Installation via Composer

``` bash
cd your/joomla/path

php composer.phar create-project asika/jconsole libraries/jconsole 1.0.*
```

Then, remove `libraries/jconsole/.gitignore` if you want to track whole Joomla by Git, use root gitignore instead.

## Getting started

Open terminal, go to `path/of/joomla`.

Type:

``` bash
php cli/console
```

Will get a help message:

```
Joomla! Console - version: 1.0
------------------------------------------------------------

[console Help]


Usage:
  console <command> [option]


Options:

  -h | --help       Display this help message.
  -q | --quiet      Do not output any message.
  -v | --verbose    Increase the verbosity of messages.
  --no-ansi         Suppress ANSI colors on unsupported terminals.


Available commands:

  help      List all arguments and show usage & manual.

  build     Some useful tools for building system.

  sql       Example description.

  system    System control.

Welcome to Joomla! Console.
```

## Available Commands

```
  help                   List all arguments and show usage & manual.

  build                  Some useful tools for building system.
      check-constants    Check php files which do not included Joomla constants.
      gen-command        Generate a command class.
      index              Create empty index.html files in directories.

  sql                    SQL migration tools.
      backup             Backup sql.
      col                Column operation
      export             Export sql.
      import             Import a sql file.
      profile            Profiles.
      restore            Restore to pervious point.
      table              Model operation.

  system                 System control.
      clean-cache        Clean system cache.
      off                Set this site offline.
      on                 Set this site online.

```

## Add your own Commands

### Use Plugin

Create a plugin in `console` group.

``` php
<?php

// no direct access
defined('_JEXEC') or die;

class plgConsoleMycommand extends JPlugin
{
	/**
     * onConsoleLoadCommand Event, called when auto added command.
     *
     * @param   string                     $context  The command class, example: 'Command\\Build\\Indexmaker'.
     * @param   JConsole\Command\JCommand  $command  The parent command, You can addArgument to it.
     *
     * @return  void
     */
    public function onConsoleLoadCommand($context, $command)
    {
        if ($context != 'Command\\System\\System')
        {
            return;
        }

        /** @var $command JCommand */
        $command->addArgument(
            'mycommand',             // Command name
            'This is my command.',   // Description
            null,                    // Options

            // Executing code.
            function($command)
            {
                $command->out('Hello World');
            }
        );
    }
}
```

Now, this custom command will added to system command.

```
Joomla! Console - version: 1.0
------------------------------------------------------------

[system Help]

System control.

Usage:
  system <command> [option]

Options:
  -h | --help       Display this help message.
  -q | --quiet      Do not output any message.
  -v | --verbose    Increase the verbosity of messages.
  --no-ansi         Suppress ANSI colors on unsupported terminals.

Available commands:
  mycommand      This is my command.    <---- Here is your command
  clean-cache    Clean system cache.
  off            Set this site offline.
  on             Set this site online.
```

We execute it.

``` bash
$ php cli/console system mycommand
```

Result

```
$ php console system mycommand
Hello World
```

## Use custom Command class

We can put our commands in plugin folder:

```
plugins/system/mycomnand
    |---  Command
    |        |--- MyCommand
    |                |--- MyCommand.php  <--- Here is our command class
    |
    |---  mycommand.php
    |---  mycommand.xml
```

Create your command class.

``` php
<?php
namespace Command\Mycommand;

use JConsole\Command\JCommand;

class Mycommand extends JCommand
{
	/**
	 * An enabled flag.
	 *
	 * @var bool
	 */
	public static $isEnabled = true;

	protected $name = 'mycommand';

	protected $description = 'This is mycommand.';

	protected function doExecute()
	{
		$this->out('Hello World.');

		return;
	}
}
```

Register command in your plugin.

``` php
public function onConsoleLoadCommand($context, $command)
{
    if ($context != 'Command\\System\\System')
    {
        return;
    }

    // Add autoload to plugin folder
    JLoader::registerNamespace('Command', __DIR__);

    // Namespace 'Command\Mycommand\Mycommand` will auto match `Command/Mycommand/Mycommand.php` path.
    $command->addArgument(new Command\Mycommand\MyCommand);
}
```

This result will be same as previous section.

## How to use Command class

Please see: https://github.com/asika32764/joomla-framework/tree/console/src/Joomla/Console

And: https://github.com/asika32764/joomla-framework/tree/console/src/Joomla/Console/Command

## Todo

1. SQL migration documentation.
2. Site Installation
3. Assets tools
4. ACL fixer

## Contribution

Welcome any Pull-request.
