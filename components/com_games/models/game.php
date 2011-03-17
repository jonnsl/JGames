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

/**
 *
 */
class GamesModelGame extends GamesModel
{
	protected function populateState()
	{
		// Initiliase variables.
		$app	= JFactory::getApplication();

		// Load the Menu Item params
		$params = new JRegistry();
		if ($menu = $app->getMenu()->getActive()) {
			$params->loadString($menu->params);
		}
		$this->setState('params', $params);
		
		$id = JRequest::getInt('id');
		$this->setState('game.id', $id);
		
		$platform = JRequest::getInt('platform');
		$this->setState('filter.platform', $platform);
	}

	public function getItem()
	{
		$query = $this->db->getQuery(true)
			->select('a.*')
			->from('#__games as a')
			->where('a.id = '.$this->getState('game.id'));
		$this->db->setQuery($query);
		
		$item = $this->db->loadObject();
		
		if ($this->db->getErrorNum() || empty($item))
		{
			JError::raiseError(404, ' Game with id: "'.$this->getState('game.id').'" don\'t exist.');
		}
		
		$item->boxarts		= new JRegistry($item->boxarts);
		$item->boxarts		= $item->boxarts->toArray();
		$item->platforms	= $this->getPlatforms();
		$item->genres		= $this->getGenres();
		
		return $item;
	}
	
	public function getPlatforms()
	{
		$query = $this->db->getQuery(true)
			->select('pc.id, pc.title, pc.alias, pc.description, pc.params')
			->from('`#__games_platform_map` AS p')
			->leftjoin('`#__categories` AS pc ON pc.id = p.platform_id')
			->where('p.game_id = '.$this->getState('game.id'))
			->order('pc.lft ASC');
		$this->db->setQuery($query);
		
		$platforms = $this->db->loadObjectList();
		
		if ($this->db->getErrorNum())
		{
			JError::raiseError(500, 'Error getting game with id:'.$this->getState('id').' '.$this->db->getErrorMsg());
		}
		
		return $platforms;
	}
	
	public function getGenres()
	{
		$query = $this->db->getQuery(true)
			->select('gc.id, gc.title, gc.alias, gc.description, gc.params')
			->from('`#__games_genre_map` AS g')
			->leftjoin('`#__categories` AS gc ON gc.id = g.genre_id')
			->where('g.game_id = '.$this->getState('game.id'))
			->order('gc.lft ASC');
		$this->db->setQuery($query);
		
		$platforms = $this->db->loadObjectList();
		
		if ($this->db->getErrorNum())
		{
			JError::raiseError(500, 'Error getting game with id:'.$this->getState('id').' '.$this->db->getErrorMsg());
		}
		
		return $platforms;
	}
}