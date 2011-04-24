<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Libraries
 * @license		GNU General Public License version 3
 */

// No direct access
defined('_JEXEC') or die;

function JGImport($filePath, $prefix = true)
{
	static $paths;
	$keyPath = 'libraries.'.$filePath;

	if (!isset($paths[$keyPath]))
	{
		$base	= JPATH_LIBRARIES.'/games/';
		$parts	= explode('.', $filePath);

		$className = array_pop($parts);
		switch ($className)
		{
			case 'helper' :
				$className = ucfirst(array_pop($parts)).ucfirst($className);
				break;
			default :
				$className = ucfirst($className);
				break;
		}

		$path = str_replace('.', DS, $filePath);

		// we are loading a JGames class prepend the classname with a capital JG.
		if ($prefix) $className = 'JG'.$className;
		$classes = JLoader::register($className, $base.$path.'.php');
		$rs = isset($classes[strtolower($className)]);

		$paths[$keyPath] = $rs;
	}

	return $paths[$keyPath];
}