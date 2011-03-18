<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Administrator
 * @license		GNU General Public License version 3
 */

// No direct access
defined('_JEXEC') or die;

JLoader::register('GamesXhtml', JPATH_LIBRARIES.'/games/html/xhtml.php');

/**
 * Utility class for games
 *
 * @static
 */
abstract class JHtmlGames
{

	public function boxart($alt, $sources, $platform = 0, $link = true)
	{
		$sources = (array) $sources;
		$pid = (int) $platform;
		if (isset($sources[$pid])){
            $src = $sources[$pid];
        }
		else {
			$platforms = self::getPlatformsByOrder();
			foreach ($platforms as $pid)
			{
				if (isset($sources[$pid]))
				{
					$src = $sources[$pid];
					break;
				}
			}
		}
		
		$src_thumb = preg_replace('/(\.jpg)$/i', '_thumb$1', $src);
		$img = new GamesXhtml('img', array('alt' => htmlspecialchars($alt, ENT_COMPAT, 'UTF-8'), 'src' => $src_thumb));
		$html = $img;
		
		if ($link) {
			JHtml::_('behavior.modal');
			$a = new GamesXhtml('a', array('title' => htmlspecialchars($alt, ENT_COMPAT, 'UTF-8'), 'href' => $src, 'class' => 'modal'));
			$a->setHtml($img);
			$html = $a;
		}
		
		return $html;
	}

	public function platformsList($game, $current = 0)
	{
		$platforms = $game->platforms;
		$html = array();
		foreach ($platforms as $platform)
		{
			$href = JRoute::_('index.php?option=com_games&view=game&id='.$game->id.'&platform='.$platform->id);
			$a = new GamesXhtml('a', array('title' => htmlspecialchars($platform->title, ENT_COMPAT, 'UTF-8'), 'href' => $href));
			if ($platform->id == $current) $a->addClass('current');
			$a->setText($platform->title);
			$html[] = (string)$a;
		}
		
		return implode(' / ', $html);
	}

	/**
	 * @todo: move this to a model.
	 */
	public function getPlatformsByOrder()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('c.id')
			->from('#__categories as c')
			->where('c.extension = '.$db->quote('com_games.platforms'))
			->order('lft ASC');
		$db->setQuery($query);
		
		// @TODO: check for a database error
		
		return $db->loadResultArray();
	}
}