<?php
/**
 * @version		$Id$
 * @package		JGames
 * @subpackage	Libraries
 * @copyright	(C) 2011 Copyleft - all rights reversed
 * @license		GNU General Public License version 3
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Base controller class for the JGames
 *
 * @since 1.0
 */
class JGController extends JObject
{
	/**
	 * The base path of the controller
	 *
	 * @var string
	 */
	protected $basePath;

	/**
	 * The default view for the display method.
	 *
	 * @var string	
	 */
	protected $default_view;

	/**
	 * The mapped task that was performed.
	 *
	 * @var string
	 */
	protected $doTask;

	/**
	 * Redirect message.
	 *
	 * @var string
	 */
	protected $message;

	/**
	 * Redirect message type.
	 *
	 * @var string
	 */
	protected $messageType = 'message';

	/**
	 * Array of class methods
	 *
	 * @var array
	 */
	protected $methods;

	/**
	 * The name of the controller
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The set of search directories for resources (views).
	 *
	 * @var array
	 */
	protected $paths = array();

	/**
	 * The prefix of the controller
	 *
	 * @var	string
	 */
	protected $prefix;

	/**
	 * URL for redirection.
	 *
	 * @var string
	 */
	protected $redirect;

	/**
	 * Current or most recent task to be performed.
	 *
	 * @var string
	 */
	protected $task;

	/**
	 * Array of class methods to call for a given task.
	 *
	 * @var array
	 */
	protected $taskMap;

	/**
	 * Adds to the stack of model paths in LIFO order.
	 *
	 * @param	string|array The directory (string), or list of directories (array) to add.
	 * @param	string	A prefix for models
	 * @return	void
	 */
	public static function addModelPath($path, $prefix = '')
	{
		JGImport('application.component.model');
		JGModel::addIncludePath($path, $prefix);
	}

	/**
	 * Create the filename for a resource.
	 *
	 * @param	string	The resource type to create the filename for.
	 * @param	array	An associative array of filename information. Optional.
	 * @return	string	The filename.
	 */
	protected static function createFileName($type, $parts = array())
	{
		$filename = '';

		switch ($type)
		{
			case 'controller':
				$parts['format'] = (empty($parts['format']) || $parts['format'] == 'html') ? '' : '.'.$parts['format'];
				$filename = strtolower($parts['name']).$parts['format'].'.php';
				break;

			case 'view':
				if (!empty($parts['type'])) {
					$parts['type'] = '.'.$parts['type'];
				}

				$filename = strtolower($parts['name']).'/view'.$parts['type'].'.php';
				break;
		}

		return $filename;
	}

	/**
	 * Method to get a singleton controller instance.
	 *
	 * @param	string	The prefix for the controller.
	 * @param	string	Default controller if none is set
	 * @param	array	An array of optional constructor options.
	 * @return	mixed	JController derivative class or JException on error.
	 */
	public static function getInstance($prefix, $controller = null, $config = array())
	{
		// Get the environment configuration.
		$basePath	= isset($config['base_path']) ? $config['base_path'] : JPATH_COMPONENT;
		$format		= JRequest::getWord('format');
		$controller	= JRequest::getCmd('controller', $controller ? $controller : $prefix);

		// Define the controller filename and path.
		$file	= self::createFileName('controller', array('name' => $controller, 'format' => $format));
		$path	= $basePath.'/controllers/'.$file;

		// Get the controller class name.
		$class = ucfirst($prefix).'Controller'.ucfirst(strtolower($controller));

		// Include the class if not present.
		if (!class_exists($class)) {
			// If the controller file path exists, include it.
			if (file_exists($path)) {
				require_once $path;
			} else {
				JError::raiseError(1056, JText::sprintf('JLIB_APPLICATION_ERROR_INVALID_CONTROLLER', $controller, $format));
			}
		}

		// Instantiate the class.
		if (class_exists($class)) {
			return new $class($config);
		}

		JError::raiseError(1057, JText::sprintf('JLIB_APPLICATION_ERROR_INVALID_CONTROLLER_CLASS', $class));
	}

	/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 * Recognized key values include 'name', 'default_task', 'model_path', and
	 * 'view_path' (this list is not meant to be comprehensive).
	 */
	public function __construct($config = array())
	{
		// Determine the methods to exclude from the base class.
		$xMethods = get_class_methods('JGController');

		// Get the public methods in this class using reflection.
		$r			= new ReflectionClass($this);
		$rName		= $r->getName();
		$rMethods	= $r->getMethods(ReflectionMethod::IS_PUBLIC);

		foreach ($rMethods as $rMethod)
		{
			$mName = $rMethod->getName();

			// Add default display method if not explicitly declared.
			if (!in_array($mName, $xMethods) || $mName == 'display') {
				$this->methods[] = strtolower($mName);
				// Auto register the methods as tasks.
				$this->taskMap[strtolower($mName)] = $mName;
			}
		}

		//set the view name
		if (empty($this->name) && isset($config['name'])) {
			$this->name = $config['name'];
		}

		// Set a base path for use by the controller
		if (empty($this->basePath)) {
			$this->basePath	= (isset($config['base_path'])) ? $config['base_path'] : JPATH_COMPONENT;
		}

		// If the default task is set, register it as such
		if (isset($config['default_task'])) {
			$this->registerDefaultTask($config['default_task']);
		} else {
			$this->registerDefaultTask('display');
		}

		// set the default model search path
		if (isset($config['model_path'])) {
			// user-defined dirs
			$this->addModelPath($config['model_path']);
		} else {
			$this->addModelPath($this->basePath.'/models');
		}

		// set the default view search path
		if (isset($config['view_path'])) {
			// user-defined dirs
			$this->setPath('view', $config['view_path']);
		} else {
			$this->setPath('view', $this->basePath.'/views');
		}

		// Set the default view.
		if (empty($this->default_view)) {
			$this->default_view	= (isset($config['default_view'])) ? $config['default_view'] : $this->getName();
		}
	}

	/**
	 * Adds to the search path for templates and resources.
	 *
	 * @param	string			The path type (e.g. 'model', 'view').
	 * @param	string|array	The directory or stream to search.
	 * @return	JController		This object to support chaining.

	 */
	protected function addPath($type, $path)
	{
		// just force path to array
		$path = (array) $path;

		if (!isset($this->paths[$type])) {
			$this->paths[$type] = array();
		}

		// loop through the path directories
		foreach ($path as $dir)
		{
			// no surrounding spaces allowed!
			$dir = rtrim(JPath::check($dir, '/'), '/').'/';

			// add to the top of the search dirs
			array_unshift($this->paths[$type], $dir);
		}

		return $this;
	}

	/**
	 * Add one or more view paths to the controller's stack, in LIFO order.
	 *
	 * @param	string|array The directory (string), or list of directories (array) to add.
	 * @return	JController		This object to support chaining.
	 */
	public function addViewPath($path)
	{
		$this->addPath('view', $path);

		return $this;
	}

	/**
	 * Method to load and return a model object.
	 *
	 * @param	string  The name of the model.
	 * @param	string	Optional model prefix.
	 * @param	array	Configuration array for the model. Optional.
	 * @return	mixed	Model object on success; otherwise null failure.
	 */
	protected function createModel($name, $prefix = '', $config = array())
	{
		JGImport('application.component.model');

		// Clean the model name
		$modelName		= preg_replace('/[^A-Z0-9_]/i', '', $name);
		$classPrefix	= preg_replace('/[^A-Z0-9_]/i', '', $prefix);

		$result = JGModel::getInstance($modelName, $classPrefix, $config);

		return $result;
	}

	/**
	 * Method to load and return a view object. This method first looks in the
	 * current template directory for a match, and failing that uses a default
	 * set path to load the view class file.
	 *
	 * Note the "name, prefix, type" order of parameters, which differs from the
	 * "name, type, prefix" order used in related public methods.
	 *
	 * @param	string	The name of the view.
	 * @param	string	Optional prefix for the view class name.
	 * @param	string	The type of view.
	 * @param	array	Configuration array for the view. Optional.
	 * @return	mixed	View object on success; null or error result on failure.
	 */
	protected function createView($name, $prefix = '', $type = '', $config = array())
	{
		// Clean the view name
		$viewName		= preg_replace('/[^A-Z0-9_]/i', '', $name);
		$classPrefix	= preg_replace('/[^A-Z0-9_]/i', '', $prefix);
		$viewType		= preg_replace('/[^A-Z0-9_]/i', '', $type);

		// Build the view class name
		$viewClass = $classPrefix . $viewName;

		if (!class_exists($viewClass)) {
			jimport('joomla.filesystem.path');
			$path = JPath::find(
				$this->paths['view'],
				$this->createFileName('view', array('name' => $viewName, 'type' => $viewType))
			);

			if ($path) {
				require_once $path;

				if (!class_exists($viewClass)) {
					$result = JError::raiseError(
						500, JText::sprintf('JLIB_APPLICATION_ERROR_VIEW_CLASS_NOT_FOUND', $viewClass, $path));
					return null;
				}
			}
			else {
				return null;
			}
		}

		return new $viewClass($config);
	}

	/**
	 * Typical view method for MVC based architecture
	 *
	 * This function is provide as a default implementation, in most cases
	 * you will need to override it in your own controllers.
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 * @return	JController		This object to support chaining.
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$document	= JFactory::getDocument();
		$viewType	= $document->getType();
		$viewName	= JRequest::getCmd('view', $this->default_view);
		$viewLayout = JRequest::getCmd('layout', 'default');

		$view = $this->getView($viewName, $viewType, '', array('base_path' => $this->basePath));

		// Get/Create the model
		if ($model = $this->getModel($viewName)) {
			// Push the model into the view (as default)
			$view->setModel($model, true);
		}

		// Set the layout
		$view->setLayout($viewLayout);

		$view->document = $document;

		// Display the view
		if ($cachable && $viewType != 'feed' && JFactory::getConfig()->get('caching') >= 1)
		{
			$option	= JRequest::getCmd('option');
			$cache	= JFactory::getCache($option, 'view');

			if (is_array($urlparams))
			{
				$app = JFactory::getApplication();

				$registeredurlparams = $app->get('registeredurlparams');

				if (empty($registeredurlparams)) {
					$registeredurlparams = new stdClass();
				}

				foreach ($urlparams as $key => $value)
				{
					// add your safe url parameters with variable type as value {@see JFilterInput::clean()}.
					$registeredurlparams->$key = $value;
				}

				$app->set('registeredurlparams', $registeredurlparams);
			}

			$cache->get($view, 'display');

		}
		else {
			$view->display();
		}

		return $this;
	}

	/**
	 * Execute a task by triggering a method in the derived class.
	 *
	 * @param	string The task to perform. If no matching task is found, the '__default' task is executed, if defined.
	 * @return	mixed|false The value returned by the called method, false in error case.
	 */
	public function execute($task = null)
	{
		$task = $task ? $task : JRequest::getCmd('task');
		$this->task = $task;

		$task = strtolower($task);
		if (isset($this->taskMap[$task])) {
			$doTask = $this->taskMap[$task];
		} elseif (isset($this->taskMap['__default'])) {
			$doTask = $this->taskMap['__default'];
		} else {
			return JError::raiseError(404, JText::sprintf('JLIB_APPLICATION_ERROR_TASK_NOT_FOUND', $task));
		}

		// Record the actual task being fired
		$this->doTask = $doTask;

		// Execute!
		return $this->$doTask();
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param	string	The model name. Optional.
	 * @param	string	The class prefix. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	object	The model.
	 */
	public function getModel($name = '', $prefix = '', $config = array())
	{
		if (empty($name)) {
			$name = $this->getName();
		}

		if (empty($prefix)) {
			$prefix = $this->getPrefix() . 'Model';
		}

		if ($model = $this->createModel($name, $prefix, $config))
		{
			// task is a reserved state
			$model->setState('task', $this->task);

			// Lets get the application object and set menu information if its available
			$app	= JFactory::getApplication();
			$menu	= $app->getMenu();

			if (is_object($menu)) {
				if ($item = $menu->getActive()) {
					$params	= $menu->getParams($item->id);
					// Set Default State Data
					$model->setState('parameters.menu', $params);
				}
			}
		}
		return $model;
	}

	/**
	 * Method to get the controller name
	 *
	 * The dispatcher name by default parsed using the classname, or it can be set
	 * by passing a $config['name'] in the class constructor
	 *
	 * @return	string The name of the controller
	 */
	public function getName()
	{
		if (empty($this->name))
		{
			if (!preg_match('/Controller([a-z]*)$/i', get_class($this), $r)) {
				JError::raiseError(500, JText::_('JLIB_APPLICATION_ERROR_CONTROLLER_GET_NAME'));
			}
			$this->name = strtolower($r[1]);
		}

		return $this->name;
	}
	
	/**
	 * Method to get the controller prefix
	 *
	 * The controller name by default parsed using the classname, or it can be set
	 * by passing a $config['prefix'] in the class constructor
	 *
	 * @return	string
	 */
	public function getPrefix()
	{
		if (empty($this->prefix))
		{
			if (!preg_match('/^([a-z]*)Controller/i', get_class($this), $r)) {
				JError::raiseError(500, JText::_('JLIB_APPLICATION_ERROR_CONTROLLER_GET_NAME'));
			}
			$this->prefix = strtolower($r[1]);
		}

		return $this->prefix;
	}

	/**
	 * Get the last task that is or was to be performed.
	 *
	 * @return	string The task that was or is being performed.
	 */
	public function getTask()
	{
		return $this->task;
	}

	/**
	 * Gets the available tasks in the controller.
	 *
	 * @return	array Array[i] of task names.
	 */
	public function getTasks()
	{
		return $this->methods;
	}

	/**
	 * Method to get a reference to the current view and load it if necessary.
	 *
	 * @param	string	The view name. Optional, defaults to the controller name.
	 * @param	string	The view type. Optional.
	 * @param	string	The class prefix. Optional.
	 * @param	array	Configuration array for view. Optional.
	 * @return	object	Reference to the view or an error.
	 */
	public function getView($name = '', $type = '', $prefix = '', $config = array())
	{
		static $views;

		if (!isset($views)) {
			$views = array();
		}

		if (empty($name)) {
			$name = $this->getName();
		}

		if (empty($prefix)) {
			$prefix = $this->getPrefix() . 'View';
		}

		if (empty($views[$name])) {
			if ($view = $this->createView($name, $prefix, $type, $config)) {
				$views[$name] = $view;
			} else {
				$result = JError::raiseError(
					500, JText::sprintf('JLIB_APPLICATION_ERROR_VIEW_NOT_FOUND', $name, $type, $prefix));
				return $result;
			}
		}

		return $views[$name];
	}

	/**
	 * Redirects the browser or returns false if no redirect is set.
	 *
	 * @return	boolean	False if no redirect exists.
	 * @since	1.5
	 */
	public function redirect()
	{
		if ($this->redirect) {
			JFactory::getApplication()
				->redirect($this->redirect, $this->message, $this->messageType);
		}

		return false;
	}

	/**
	 * Register the default task to perform if a mapping is not found.
	 *
	 * @param	string		The name of the method in the derived class to perform if a named task is not found.
	 * @return	JController	This object to support chaining.
	 */
	public function registerDefaultTask($method)
	{
		$this->registerTask('__default', $method);

		return $this;
	}

	/**
	 * Register (map) a task to a method in the class.
	 *
	 * @param	string		The task.
	 * @param	string		The name of the method in the derived class to perform for this task.
	 * @return	JController	This object to support chaining.
	 */
	public function registerTask($task, $method)
	{
		if (in_array(strtolower($method), $this->methods)) {
			$this->taskMap[strtolower($task)] = $method;
		}

		return $this;
	}
	/**
	 * Unregister (unmap) a task in the class.
	 *
	 * @param	string		The task.
	 * @return	JController	This object to support chaining.
	 */
	public function unregisterTask($task)
	{
		unset($this->taskMap[strtolower($task)]);

		return $this;
	}

	/**
	 * Sets the internal message that is passed with a redirect
	 *
	 * @param	string	Message to display on redirect.
	 * @param	string	Message type (since 1.6). Optional, defaults to 'message'.
	 * @return	string	Previous message
	 */
	public function setMessage($text, $type = 'message')
	{
		$previous			= $this->message;
		$this->message		= $text;
		$this->messageType	= $type;

		return $previous;
	}

	/**
	 * Sets an entire array of search paths for resources.
	 *
	 * @param	string			The type of path to set, typically 'view' or 'model'.
	 * @param	string|array	The new set of search paths. If null or false, resets to the current directory only.
	 */
	protected function setPath($type, $path)
	{
		// clear out the prior search dirs
		$this->paths[$type] = array();

		// actually add the user-specified directories
		$this->addPath($type, $path);
	}

	/**
	 * Set a URL for browser redirection.
	 *
	 * @param   string  $url   URL to redirect to.
	 * @param   string  $msg   Message to display on redirect. Optional, defaults to value set internally by controller, if any.
	 * @param   string  $type  Message type. Optional, defaults to 'message' or the type set by a previous call to setMessage.
	 * @return  JController  This object to support chaining.
	 */
	public function setRedirect($url, $msg = null, $type = null)
	{
		$this->redirect = $url;
		if ($msg !== null) {
			// controller may have set this directly
			$this->message	= $msg;
		}

		// Ensure the type is not overwritten by a previous call to setMessage.
		$this->messageType	= ($type === null || empty($this->messageType)) ? 'message' : $type;

		return $this;
	}
}
