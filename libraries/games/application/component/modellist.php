<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Libraries
 * @license		GNU General Public License version 3
 */

// No direct access
defined('_JEXEC') or die;

JGImport('application.component.model');
JGImport('utilities.inflect');

/**
 *
 */
class JGModelList extends JGModel
{
	/**
	 * Internal memory based cache array of data.
	 *
	 * @var	array
	 */
	protected $cache = array();

	/**
	 * Context string for the model type.  This is used to handle uniqueness
	 * when dealing with the getStoreId() method and caching data structures.
	 *
	 * @var	string
	 */
	protected $context;
	
	protected $events = array('group'=>'', 'change_state'=>'', 'before_delete'=>'', 'after_delete'=>'');

	/**
	 * An internal cache for the last query used.
	 *
	 * @var	JDatabaseQuery
	 */
	protected $query = array();

	/**
	 * Constructor.
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		
		$view_item = JGInflect::singularize($this->getName());

		// Guess the context as Option.ModelName.
		if (empty($this->context)) {
			$this->context = isset($config['context']) ? $config['context'] : strtolower($this->option.'.'.$this->getName());
		}
		
		if (empty($this->events['group'])) {
			$this->events['group'] = isset($config['events']['group']) ? $config['events']['group'] : $this->getPrefix();
		}
		
		if (empty($this->events['change_state'])) {
			$this->events['change_state'] = isset($config['events']['change_state']) ? 
			$config['events']['change_state'] : 'on'.$view_item.'ChangeState';
		}
		
		if (empty($this->events['before_delete'])) {
			$this->events['before_delete'] = isset($config['events']['before_delete']) ? 
			$config['events']['before_delete'] :'on'.$view_item.'BeforeDelete';
		}
		
		if (empty($this->events['after_delete'])) {
			$this->events['after_delete'] = isset($config['events']['after_delete']) ? 
			$config['events']['after_delete'] : 'on'.$view_item.'AfterDelete';
		}
		
		if (empty($this->events['getQuery'])) {
			$this->events['getQuery'] = isset($config['events']['getQuery']) ? 
			$config['events']['getQuery'] : 'on'.ucfirst($view_item).'GetQuery';
		}
		
		if (empty($this->events['getStoreId'])) {
			$this->events['getStoreId'] = isset($config['events']['getStoreId']) ? 
			$config['events']['getStoreId'] : 'on'.ucfirst($view_item).'GetStoreId';
		}
	}

	/**
	 * Method to cache the last query constructed.
	 *
	 * This method ensures that the query is contructed only once for a given state of the model.
	 *
	 * @return	JDatabaseQuery
	 */
	private function _getListQuery()
	{
		// Capture the last store id used.
		static $lastStoreId;

		// Compute the current store id.
		$currentStoreId = $this->getStoreId();

		// If the last store id is different from the current, refresh the query.
		if ($lastStoreId != $currentStoreId || empty($this->query)) {
			$lastStoreId = $currentStoreId;
			$this->query = $this->getListQuery();
		}
		
		// Include the content plugins for the on delete events.
		JPluginHelper::importPlugin($this->events['group']);
		
		JDispatcher::getInstance()->trigger($this->events['getQuery'], array($this->query));

		return $this->query;
	}

	/**
	 * Method to get an array of data items.
	 *
	 * @return	mixed	An array of data items on success, false on failure.
	 */
	public function getItems()
	{
		// Get a storage key.
		$store = $this->getStoreId();

		// Try to load the data from internal storage.
		if (!empty($this->cache[$store])) {
			return $this->cache[$store];
		}

		// Load the list items.
		$query	= $this->_getListQuery();
		$items	= $this->getList($query, $this->getState('list.start'), $this->getState('list.limit'));

		// Check for a database error.
		if ($this->db->getErrorNum()) {
			$this->setError($this->db->getErrorMsg());
			return false;
		}

		// Add the items to the internal cache.
		$this->cache[$store] = $items;

		return $this->cache[$store];
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
		$result = $this->db->loadObjectList();

		return $result;
	}

	/**
	 * Returns a record count for the query
	 *
	 * @param	string	The query
	 * @return	int
	 */
	protected function getListCount($query)
	{
		$this->db->setQuery($query);
		$this->db->query();

		return $this->db->getNumRows();
	}

	/**
	 * Method to get a JDatabaseQuery object for retrieving the data set from a database.
	 *
	 * @return	object
	 */
	protected function getListQuery()
	{
		return $this->db->getQuery(true);
	}

	/**
	 * Method to get a JPagination object for the data set.
	 *
	 * @return	object
	 */
	public function getPagination()
	{
		// Get a storage key.
		$store = $this->getStoreId('getPagination');

		// Try to load the data from internal storage.
		if (!empty($this->cache[$store])) {
			return $this->cache[$store];
		}

		// Create the pagination object.
		jimport('joomla.html.pagination');
		$limit = (int) $this->getState('list.limit') - (int) $this->getState('list.links');
		$page = new JPagination($this->getTotal(), (int) $this->getState('list.start'), $limit);

		// Add the object to the internal cache.
		$this->cache[$store] = $page;

		return $this->cache[$store];
	}

	/**
	 * Method to get a store id based on the model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string	An identifier string to generate the store id.
	 * @return	string
	 */
	protected function getStoreId($id = '')
	{
		// Include the content plugins for the on delete events.
		JPluginHelper::importPlugin($this->events['group']);
		
		JDispatcher::getInstance()->trigger($this->events['getStoreId'], array(&$id));
		
		// Add the list state to the store id.
		$id	.= ':'.$this->getState('list.start');
		$id	.= ':'.$this->getState('list.limit');
		$id	.= ':'.$this->getState('list.ordering');
		$id	.= ':'.$this->getState('list.direction');

		return md5($this->context.':'.$id);
	}

	/**
	 * Method to get the total number of items for the data set.
	 *
	 * @return	integer	The total number of items available in the data set.
	 */
	public function getTotal()
	{
		// Get a storage key.
		$store = $this->getStoreId('getTotal');

		// Try to load the data from internal storage.
		if (!empty($this->cache[$store])) {
			return $this->cache[$store];
		}

		// Load the total.
		$query = $this->_getListQuery();
		$total = (int) $this->getListCount((string) $query);

		// Check for a database error.
		if ($this->db->getErrorNum()) {
			$this->setError($this->db->getErrorMsg());
			return false;
		}

		// Add the total to the internal cache.
		$this->cache[$store] = $total;

		return $this->cache[$store];
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param	string	An optional ordering field.
	 * @param	string	An optional direction (asc|desc).
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();
		
		// If the ordering and direction is set, assume that stateful lists are used.
		if ($ordering && $direction) {
			$value = $app->getUserStateFromRequest($this->context.'.ordercol', 'filter_order', $ordering);
			if($value == '') $value = $ordering;
			$this->setState('list.ordering', $value);

			// Check if the ordering direction is valid, otherwise use the incoming value.
			$value = $app->getUserStateFromRequest($this->context.'.orderdirn', 'filter_order_Dir', $direction);
			if (!in_array(strtoupper($value), array('ASC', 'DESC', ''))) {
				$value = $direction;
				$app->setUserState($this->context.'.orderdirn', $value);
			}
			$this->setState('list.direction', $value);
		}
		
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
		$this->setState('list.limit', $limit);

		$value = $app->getUserStateFromRequest($this->context.'.limitstart', 'limitstart', 0);
		$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
		$this->setState('list.start', $limitstart);
	}

	/**
	 * Method to change the published state of one or more records.
	 *
	 * @param	array	$pks	A list of the primary keys to change.
	 * @param	int		$value	The value of the published state.
	 * @return	boolean	True on success.
	 */
	public function publish($pks, $value = 1)
	{
		// Initialise variables.
		$dispatcher	= JDispatcher::getInstance();
		$user		= JFactory::getUser();
		$table		= $this->getTable();
		$pks		= (array) $pks;

		// Include the content plugins for the change of state event.
		JPluginHelper::importPlugin($this->events['group']);

		// Attempt to change the state of the records.
		if (!$table->publish($pks, $value, $user->get('id'))) {
			$this->setError($table->getError());
			return false;
		}

		// Trigger the onContentChangeState event.
		$result = $dispatcher->trigger($this->events['change_state'], array($this->option.'.'.$this->name, $pks, $value));

		if (in_array(false, $result, true)) {
			$this->setError($table->getError());
			return false;
		}

		return true;
	}

	/**
	 * Method to delete one or more records.
	 *
	 * @param	array	$pks	An array of record primary keys.
	 * @return	boolean	True if successful, false if an error occurs.
	 */
	public function delete($pks)
	{
		// Initialise variables.
		$dispatcher	= JDispatcher::getInstance();
		$user		= JFactory::getUser();
		$pks		= (array) $pks;
		$table		= $this->getTable();

		// Include the content plugins for the on delete events.
		JPluginHelper::importPlugin($this->events['group']);

		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk) {

			if ($table->load($pk)) {
				$context = $this->option.'.'.$this->name;

				// Trigger the onContentBeforeDelete event.
				$result = $dispatcher->trigger($this->events['before_delete'], array($context, $table));
				if (in_array(false, $result, true)) {
					$this->setError($table->getError());
					return false;
				}

				if (!$table->delete($pk)) {
					$this->setError($table->getError());
					return false;
				}

				// Trigger the onContentAfterDelete event.
				$dispatcher->trigger($this->events['after_delete'], array($context, $table));

			} else {
				$this->setError($table->getError());
				return false;
			}
		}

		return true;
	}
}