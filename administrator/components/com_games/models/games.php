<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Administrator
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

JGImport('application.component.modelList');

/**
 *
 */
class GamesModelGames extends JGModelList
{
	/**
	 * Method to auto-populate the model state.
	 */
	protected function populateState()
	{
		// Initialise variables.
		$app		= JFactory::getApplication();

		$search = $app->getUserStateFromRequest($this->context.'.search', 'filter_search');
		$this->setState('filter.search', $search);

		$serie = $app->getUserStateFromRequest($this->context.'.filter.serie', 'filter_serie', '', 'string');
		$this->setState('filter.serie', $serie);

		$developer = $app->getUserStateFromRequest($this->context.'.filter.developer', 'filter_developer', '', 'string');
		$this->setState('filter.developer', $developer);

		$publisher = $app->getUserStateFromRequest($this->context.'.filter.publisher', 'filter_publisher', '', 'string');
		$this->setState('filter.publisher', $publisher);

		$genre = $app->getUserStateFromRequest($this->context.'.filter.genre', 'filter_genre', '', 'string');
		$this->setState('filter.genre', $genre);

		$platform = $app->getUserStateFromRequest($this->context.'.filter.platform', 'filter_platform', '', 'string');
		$this->setState('filter.platform', $platform);

		// List state information.
		parent::populateState('a.title', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * @param	string	$id	A prefix for the store id.
	 * @return	string
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.serie');
		$id	.= ':'.$this->getState('filter.developer');
		$id	.= ':'.$this->getState('filter.publisher');
		$id	.= ':'.$this->getState('filter.genre');
		$id	.= ':'.$this->getState('filter.platform');


		return parent::getStoreId($id);
	}

	/**
	 *
	 *
	 * @return	string
	 */
	function getListQuery()
	{
		// Create a new query object.
		$db = $this->db;
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.*'
			)
		);
		$query->from('#__games AS a');

		// Join over the platform
		$query->select('GROUP_CONCAT(DISTINCT pc.title SEPARATOR '.$db->Quote("\n").') AS platforms');
		$query->leftjoin('`#__games_platform_map` AS p ON p.game_id = a.id');
		$query->leftjoin('`#__categories` AS pc ON pc.id = p.platform_id');

		// Join over the genres
		$query->select('GROUP_CONCAT(DISTINCT gc.title SEPARATOR '.$db->Quote("\n").') AS genres');
		$query->leftjoin('`#__games_genre_map` AS g ON g.game_id = a.id');
		$query->leftjoin('`#__categories` AS gc ON gc.id = g.genre_id');

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
				$query->where('(a.title LIKE '.$search.' OR a.alias LIKE '.$search.' )');
			}
		}

		// Filter by serie
		if ($serie = $this->getState('filter.serie')) {
			$query->where('a.serie = '.$db->quote($serie));
		}

		// Filter by developer
		if ($developer = $this->getState('filter.developer')) {
			$query->where('a.developer = '.$db->quote($developer));
		}

		// Filter by publisher
		if ($publisher = $this->getState('filter.publisher')) {
			$query->where('a.publisher = '.$db->quote($publisher));
		}

		// Filter by genre
		if ($genre = $this->getState('filter.genre')) {
			$query->where('g.genre_id = '.(int) $genre);
		}

		// Filter by platform
		if ($platform = $this->getState('filter.platform')) {
			$query->where('p.platform_id = '.(int) $platform);
		}

		// Group by id
		$query->group('a.id');

		// Add the list ordering clause.
		$query->order($db->getEscaped($this->getState('list.ordering', 'a.title')).' '.$db->getEscaped($this->getState('list.direction', 'ASC')));

		// echo nl2br(str_replace('#__','jos_',$query));
		return $query;
	}

	public function getSeries()
	{
		$options = array();
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('DISTINCT serie');
		$query->from('#__games');
		$db->setQuery($query);
		$results = $db->loadResultArray();

		//TODO add error check here

		foreach($results as $k => $v) {
			if($v != '') {
				$options[$k]['text'] = $v;
				$options[$k]['value'] = $v;
			}
		}

		return $options;
	}

	public function getDevelopers()
	{
		$options = array();
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('DISTINCT developer');
		$query->from('#__games');
		$db->setQuery($query);
		$results = $db->loadResultArray();

		//TODO add error check here

		foreach($results as $k => $v) {
			if($v != '') {
				$options[$k]['text'] = $v;
				$options[$k]['value'] = $v;
			}
		}


		return $options;
	}

	public function getPublishers()
	{
		$options = array();
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('DISTINCT publisher');
		$query->from('#__games');
		$db->setQuery($query);
		$results = $db->loadResultArray();

		//TODO add error check here

		foreach($results as $k => $v) {
			if($v != '') {
				$options[$k]['text'] = $v;
				$options[$k]['value'] = $v;
			}
		}

		return $options;
	}

	public function getTable()
	{
		return parent::getTable('game');
	}
}