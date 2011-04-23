<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Site
 * @license		GNU General Public License version 3
 */

defined('_JEXEC') or die;

/**
 * Build the route for the com_games component
 *
 * @param	array	An array of URL arguments
 * @return	array	The URL arguments to use to assemble the subsequent URL.
 */
function GamesBuildRoute(&$query)
{
	$segments = array();
	if(isset($query['view']) && isset($query['id']))
	{
		$db = JFactory::getDbo();
		$dbquery = $db->getQuery(true)
			->select('a.alias')
			->from('#__games AS a')
			->where('a.id = '.(int)$query['id']);
		$db->setQuery($dbquery);
		$segments[] = $db->loadResult();
		unset($query['id']);

		if(isset($query['platform']))
		{
			$db = JFactory::getDbo();
			$dbquery = $db->getQuery(true)
				->select('a.alias')
				->from('#__categories AS a');
			if($query['platform'] != 0) {
				$dbquery->where('a.id = '.(int)$query['platform']);
			} else {
				$dbquery->where('a.extension = "com_games.platforms"')
					->order('lft ASC');
			}
			$db->setQuery($dbquery);
			$segments[] = $db->loadResult();
			unset($query['platform']);
		}
		
		if($query['view'] !== 'game')
		{
			$segments[] = $query['view'];
		}
		unset($query['view']);
	}
	//JError::raiseWarning(0, 'BuildRoute: <pre>'.print_r($query,1).'</pre>--------------------');
	return $segments;
}

/**
 * Parse the segments of a URL.
 *
 * @param	array	The segments of the URL to parse.
 * @return	array	The URL attributes to be used by the application.
 */
function GamesParseRoute($segments)
{
	if (isset($segments[0]))
	{
		$vars['view'] = 'game';
		$game = str_replace(':', '-',$segments[0]);
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('a.id')
			->from('#__games AS a')
			->where('a.alias = '.$db->quote($game));
		$db->setQuery($query);
		$vars['id'] = (int)$db->loadResult();
		$vars['game_alias'] = $game;
	}
	if (isset($segments[1]))
	{
		$platform = str_replace(':', '-',$segments[1]);
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('pc.id')
			->from('#__categories AS pc')
			->leftjoin('#__games_platform_map AS p ON pc.id = p.platform_id')
			->where('p.game_id = '.$vars['id'])
			->where('pc.alias = '.$db->quote($platform));
		$db->setQuery($query);
		$vars['platform'] = (int)$db->loadResult();
		if (!$vars['platform']) {
			JError::raiseError(404, 'Game not avaiable on this platform');
		}
	}
	if (isset($segments[2]))
	{
		switch ($segments[2])
		{
			case 'achievements':
				$vars['view'] = $segments[2];
				break;
		}
		
	}
	//JError::raiseWarning(0, 'GamesParseRoute: <pre>'.print_r($segments,1).'</pre>');
	return $vars;
}
