<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Site
 * @license		GNU General Public License version 3
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
		// Initiliase variables.
		$app	= JFactory::getApplication();

		// Load the Menu Item params
		$params = new JRegistry();
		if ($menu = $app->getMenu()->getActive()) {
			$params->loadString($menu->params);
		}
		$this->setState('params', $params);

		$layout = JRequest::getcmd('layout', '');
		$tmpl = JRequest::getcmd('tmpl', '');
		$search = ($layout == 'modal' && $tmpl = 'component') ?
			JRequest::getString('filter_search', '')
			: false;
		$this->setState('filter.search', $search);
		
		$serie = JRequest::getString('serie', '');
		$this->setState('filter.serie', $serie);

		$developer = JRequest::getString('developer', '');
		$this->setState('filter.developer', $developer);

		$publisher = JRequest::getString('publisher', '');
		$this->setState('filter.publisher', $publisher);

		$genre = JRequest::getString('genre', '');
		$this->setState('filter.genre', $genre);

		$platform = JRequest::getString('platform', '');
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
		$id	.= ':'.$this->getState('filter.series');
		$id	.= ':'.$this->getState('filter.developers');
		$id	.= ':'.$this->getState('filter.publishers');
		$id	.= ':'.$this->getState('filter.genres');
		$id	.= ':'.$this->getState('filter.platforms');


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

		// Filter by serie
		$serie = $this->getState('filter.serie');
		//$series = $this->getState('filter.series');
		//$series = array_filter($series);
		if (/*!empty($series)*/$serie != '') {
			$query->where('a.serie ='.$db->quote($serie));
			//$series = array_map(array($db, 'quote'), $series);
			//$query->where('a.serie IN ('.implode(',', $series).')');
		}

		// Filter by developer
		$developer = $this->getState('filter.developer');
		//$developers = $this->getState('filter.developers');
		//$developers = array_filter($developers);
		if (/*!empty($developers)*/$developer != '') {
			$query->where('a.developer ='.$db->quote($developer));
			//$developers = array_map(array($db, 'quote'), $developers);
			//$query->where('a.developer IN ('.implode(',', $developers).')');
		}

		// Filter by publisher
		$publisher = $this->getState('filter.publisher');
		//$publishers = $this->getState('filter.publishers');
		//$publishers = array_filter($publishers);
		if (/*!empty($publishers)*/$publisher != '') {
			$query->where('a.publisher ='.$db->quote($publisher));
			//$publishers = array_map(array($db, 'quote'), $publishers);
			//$query->where('a.publisher IN ('.implode(',', $publishers).')');
		}

		// Filter by genre
		$genre = $this->getState('filter.genre');
		//$genres = $this->getState('filter.genres');
		//$genres = array_filter($genres);
		if (/*!empty($genres)*/$genre != '') {
			$query->where('g.genre_id ='.$db->quote($genre));
			//$genres = array_map(array($db, 'quote'), $genres);
			//$query->where('g.genre_id IN ('.implode(',', $genres).')');
		}

		// Filter by platform
		$platform = $this->getState('filter.platform');
		//$platforms = $this->getState('filter.platforms');
		//$platforms = array_filter($platforms);
		if (/*!empty($platforms)*/$platform != '') {
			$query->where('p.platform_id ='.$db->quote($platform));
			//$platforms = array_map(array($db, 'quote'), $platforms);
			//$query->where('p.platform_id IN ('.implode(',', $platforms).')');
		}

		// Filter by platform
		$search = $this->getState('filter.search');
		if ($search) {
			$search = $db->Quote('%'.$db->getEscaped($search, true).'%', false);
			$query->where('a.title LIKE '.$search);
		}

		// Group by id
		$query->group('a.id');

		// Add the list ordering clause.
		$query->order($db->getEscaped($this->getState('list.ordering', 'a.title')).' '.$this->getState('list.direction', 'ASC'));

		//echo nl2br(str_replace('#__','jos_',$query));
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
	
	public function getOrdersOptions()
	{
		return array(
			array('text' => 'COM_GAMES_OPTION_ORDER_BY_TITLE', 'value' => 'a.title'),
			array('text' => 'COM_GAMES_OPTION_ORDER_BY_SERIE', 'value' => 'a.serie'),
			array('text' => 'COM_GAMES_OPTION_ORDER_BY_DEVELOPER', 'value' => 'a.developer'),
			array('text' => 'COM_GAMES_OPTION_ORDER_BY_PUBLISHER', 'value' => 'a.publisher')
		);
	}

	public function getTable()
	{
		return parent::getTable('game');
	}
}