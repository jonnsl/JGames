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
class GamesModelAjax extends JGModelList
{
	/**
	 * Method to auto-populate the model state.
	 */
	protected function populateState()
	{
		// Query to search for
		$q = JRequest::getString('q', '');
		$this->setState('filter.q', $q);

		// Limit
		$limit = JRequest::getInt('limit', 20);
		$this->setState('list.limit', $limit);

		// Start
		$this->setState('list.start', 0);
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
		$id	.= ':'.$this->getState('filter.type');
		$id	.= ':'.$this->getState('filter.q');
		$id	.= ':'.$this->getState('list.limit');
		$id	.= ':'.$this->getState('list.start');

		return md5($id);
	}

	/**
	 *	Method to get a JDatabaseQuery object for retrieving the data set from a database.
	 *
	 * @return	object
	 */
	protected function getListQuery()
	{
		// Initialise variables
		$db = $this->db;
		$query = $db->getQuery(true);
		$type = $this->getState('filter.type');

		$query->select(
			'DISTINCT a.'.$type
		);
		$query->from('#__games AS a');
		$query->where('a.'.$type.' LIKE '.$this->db->quote($this->getState('filter.q').'%'));

		// echo nl2br(str_replace('#__','jos_',$query));
		return $query;


	}

	/**
	 * Returns an object list
	 *
	 * @param	string	The query
	 * @param	int		Offset
	 * @param	int		The number of records
	 * @return	array
	 */
	protected function getList($query, $limitstart = 0, $limit = 0)
	{
		$this->db->setQuery($query, $limitstart, $limit);
		$results = JGArrayHelper::excludeEmptyValues($this->db->loadResultArray());
		$results = JGArrayHelper::resetKeys($results);

		return $results;
	}

}