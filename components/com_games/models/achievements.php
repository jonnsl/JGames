<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Site
 * @license		GNU General Public License version 3
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.form.form');
JGImport('application.component.model');
require dirname(__FILE__).'/game.php';


/**
 *
 */
class GamesModelAchievements extends GamesModelGame
{
	public function getItem()
	{
		$this->setState('game.select', 'a.id, a.title, a.boxarts, a.description, a.developer, a.publisher, a.serie, a.site, a.achievements');
		$item = parent::getItem();
		$item->achievements = unserialize($item->achievements);
		return $item;
	}
}