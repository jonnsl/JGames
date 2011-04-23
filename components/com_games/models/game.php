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

/**
 *
 */
class GamesModelGame extends JGModel
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
		$this->setState('params.menu', $params);

		$id = JRequest::getInt('id');
		$this->setState('game.id', $id);
		
		$platform = JRequest::getInt('platform');
		$this->setState('filter.platform', $platform);
	}
	
	public function getItem()
	{
		$query = $this->db->getQuery(true)
			->select($this->getState('game.select', 'a.id, a.title, a.boxarts, a.description, a.developer, a.publisher, a.serie, a.site'))
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
			->select('pc.id, pc.title, pc.params')
			->from('#__games_platform_map AS p')
			->leftjoin('#__categories AS pc ON pc.id = p.platform_id')
			->where('p.game_id = '.$this->getState('game.id'))
			->order('pc.lft ASC');
		$this->db->setQuery($query);
		
		$platforms = $this->db->loadObjectList('id');
		
		if ($this->db->getErrorNum())
		{
			JError::raiseError(500, 'Error getting game with id:'.$this->getState('id').' '.$this->db->getErrorMsg());
		}
		
		foreach ($platforms as $k => $v)
		{
			$platforms[$k]->params = new JRegistry($v->params);
		}

		// set the platform params, set the params from the first platform if none is set
		$platform = $this->getState('filter.platform', 0);
		if (isset($platforms[$platform])) {
			$params = $platforms[$platform]->params;
		} else {
			// FIXME: look at the documentation
			//$params = function_that_returns_the_first_element_of_an_array($platforms)->params;
			foreach ($platforms as $v)
			{
				$params = $v->params;
				break;
			}
		}
		$this->setState('params.platform', $params);
		
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