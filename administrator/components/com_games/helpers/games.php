<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Administrator
 * @license		GNU General Public License version 3
 */

// No direct access
defined('_JEXEC') or die;

/**
 *
 */
class GamesHelper extends JObject
{
	public static function addSubmenu($vName)
	{
		JSubMenuHelper::addEntry(
			JText::_('COM_GAMES_GAMES'),
			'index.php?option=com_games&view=games',
			$vName == 'games'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_GAMES_GENRES'),
			'index.php?option=com_categories&extension=com_games.genres',
			$vName == 'categories.genres'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_GAMES_PLATFORMS'),
			'index.php?option=com_categories&extension=com_games.platforms',
			$vName == 'categories.platforms'
		);
	}
}