<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Libraries
 * @license		GNU General Public License version 3
 */

// No direct access
defined('_JEXEC') or die;

/**
 *
 */
class GamesModel extends JObject
{
	/**
	 * Database Connector
	 *
	 * @var	JDatabase
	 */
	protected $db;

	/**
	 * Indicates if the internal state has been set
	 *
	 * @var boolean
	 */
	private $_state_set = false;

	/**
	 * The model name
	 *
	 * @var	string
	 */
	protected $name;

	/**
	 * The URL option for the component
	 *
	 * @var	string
	 */
	protected $option;

	/**
	 * An state object
	 *
	 * @var	JObject
	 */
	protected $state;

	/**
	 * Add a directory where JModel should search for models. You may
	 * either pass a string or an array of directories.
	 *
	 * @param	string	A path to search.
	 * @return	array	An array with directory elements
	 */
	public static function addIncludePath($path = '')
	{
		static $paths;

		if (!isset($paths)) {
			$paths = array();
		}
		if (!empty($path) && !in_array($path, $paths)) {
			jimport('joomla.filesystem.path');
			array_unshift($paths, JPath::clean($path));
		}
		return $paths;
	}

	/**
	 * Adds to the stack of model table paths in LIFO order.
	 *
	 * @param	string|array The directory (-ies) to add.
	 * @return	void
	 */
	public static function addTablePath($path)
	{
		jimport('joomla.database.table');
		JTable::addIncludePath($path);
	}

	/**
	 * Create the filename for a resource
	 *
	 * @param	string	The resource type to create the filename for
	 * @param	array	An associative array of filename information
	 * @return	string	The filename
	 */
	private static function _createFileName($type, $parts = array())
	{
		$filename = '';

		switch($type) {
			case 'model':
				$filename = strtolower($parts['name']).'.php';
				break;

		}
		return $filename;
	}

	/**
	 * Returns a Model object, always creating it
	 *
	 * @param	string	The model type to instantiate
	 * @param	string	Prefix for the model class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	mixed	A model object, or false on failure
	 */
	public static function getInstance($type, $prefix = '', $config = array())
	{
		$type		= preg_replace('/[^A-Z0-9_\.-]/i', '', $type);
		$modelClass	= $prefix.ucfirst($type);

		if (!class_exists($modelClass)) {
			jimport('joomla.filesystem.path');
			$path = JPath::find(
				JModel::addIncludePath(),
				JModel::_createFileName('model', array('name' => $type))
			);
			if ($path) {
				require_once $path;

				if (!class_exists($modelClass)) {
					JError::raiseWarning(0, JText::sprintf('JLIB_APPLICATION_ERROR_MODELCLASS_NOT_FOUND', $modelClass ));
					return false;
				}
			}
			else return false;
		}

		return new $modelClass($config);
	}

	/**
	 * Constructor
	 */
	public function __construct($config = array())
	{
		// Set the option
		if (empty($this->option)) {
			$this->option = isset($config['option']) ? $config['option'] : 'com_'.$this->getPrefix();
		}

		// Set the view name
		if (empty($this->name) && isset($config['name'])) {
			$this->name = $config['name'];
		}

		// Set the model state
		if (empty($this->state)) {
			$this->state = isset($config['state']) ? $config['state'] : new JObject;
		}

		// Set the model dbo
		if (empty($this->db)) {
			$this->db = isset($config['dbo']) ? $config['dbo'] : JFactory::getDbo();
		}

		// Set the default view search path
		if (isset($config['table_path'])) {
			$this->addTablePath($config['table_path']);
		} else if (defined('JPATH_COMPONENT_ADMINISTRATOR')){
			$this->addTablePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
		}

		// Set the internal state marker - used to ignore setting state from the request
		if (!empty($config['ignore_request'])) {
			$this->_state_set = true;
		}
	}

	/**
	 * Method to load and return a model object.
	 *
	 * @param	string	The name of the view
	 * @param	string  The class prefix. Optional.
	 * @return	mixed	Model object or boolean false if failed
	 */
	private function _createTable($name, $prefix = 'JTable', $config = array())
	{
		// Clean the model name
		$name	= preg_replace('/[^A-Z0-9_]/i', '', $name);
		$prefix = preg_replace('/[^A-Z0-9_]/i', '', $prefix);

		//Make sure we are returning a DBO object
		if (!isset($config['dbo']))  {
			$config['dbo'] = $this->db;
		}

		return JTable::getInstance($name, $prefix, $config);;
	}

	/**
	 * Method to get the model name
	 *
	 * The model name by default parsed using the classname, or it can be set
	 * by passing a $config['name'] in the class constructor
	 *
	 * @return	string
	 */
	public function getName()
	{
		if (empty($this->name)) {
			$r = null;
			if (!preg_match('/Model([a-z]*)$/i', get_class($this), $r)) {
				JError::raiseError (500, 'JLIB_APPLICATION_ERROR_MODEL_GET_NAME');
			}
			$this->name = strtolower($r[1]);
		}

		return $this->name;
	}

	/**
	 * Method to get the model prefix
	 *
	 * The model prefix by default parsed using the classname, or it can be set
	 * by passing a $config['prefix'] in the class constructor
	 *
	 * @return	string
	 */
	public function getPrefix()
	{
		if (empty($this->prefix)) {
			$r = null;
			if (!preg_match('/^([a-z]*)Model/i', get_class($this), $r)) {
				JError::raiseError (500, 'JLIB_APPLICATION_ERROR_MODEL_GET_NAME');
			}
			$this->prefix = strtolower($r[1]);
		}

		return $this->prefix;
	}

	/**
	 * Method to get model state variables
	 *
	 * @param	string	Optional parameter name
	 * @param	mixed	Optional default value
	 * @return	object	The property where specified, the state object where omitted
	 */
	public function getState($property = null, $default = null)
	{
		if (!$this->_state_set) {
			// Private method to auto-populate the model state.
			$this->populateState();

			// Set the model state set flat to true.
			$this->_state_set = true;
		}

		return $property === null ? $this->state : $this->state->get($property, $default);
	}

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param	string	The table name. Optional.
	 * @param	string	The class prefix. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	object
	 */
	public function getTable($name = '', $prefix = 'JTable', $options = array())
	{
		if (empty($name)) {
			$name = $this->getName();
		}

		if ($table = $this->_createTable($name, $prefix, $options))  {
			return $table;
		}

		JError::raiseError(0, JText::sprintf('JLIB_APPLICATION_ERROR_TABLE_NAME_NOT_SUPPORTED', $name));

		return null;
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
	 * @return	void
	 */
	protected function populateState() {}

	/**
	 * Method to set model state variables
	 *
	 * @param	string	The name of the property
	 * @param	mixed	The value of the property to set
	 * @return	mixed	The previous value of the property
	 */
	public function setState($property, $value = null)
	{
		return $this->state->set($property, $value);
	}
}