<?php

// Register namespaces in vendor.
$folders = new DirectoryIterator(__DIR__);

foreach ($folders as $folder)
{
	if (!$folder->isDir())
	{
		continue;
	}

	JLoader::registerNamespace($folder->getBasename(),   __DIR__);
}

// Register namespaces in src.
$folders = new DirectoryIterator(JPATH_CLI . '/jconsole/src');

foreach ($folders as $folder)
{
	if (!$folder->isDir())
	{
		continue;
	}

	JLoader::registerNamespace($folder->getBasename(),   JPATH_CLI . '/jconsole/src');
}