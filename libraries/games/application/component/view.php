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
class JGView extends JObject
{
	/**
	 * The name of the view
	 *
	 * @var string
	 */
	protected $_name;

	/**
	 * The prefix of the view
	 *
	 * @var	string
	 */
	protected $_prefix;

	/**
	 * Registered models
	 *
	 * @var array
	 */
	protected $_models = array();

	/**
	 * The base path of the view
	 *
	 * @var string
	 */
	protected $_basePath;

	/**
	 * The default model
	 *
	 * @var	string
	 */
	protected $_defaultModel;

	/**
	 * Layout name
	 *
	 * @var string
	 */
	protected $_layout = 'default';

	/**
	 * Layout extension
	 *
	 * @var string
	 */
	protected $_layoutExt = 'php';

	/**
	 * Layout template
	 *
	 * @var string
	 */
	protected $_layoutTemplate = '_';

	/**
	* The set of search directories for resources (templates)
	*
	* @var array
	*/
	protected $_path = array(
		'template' => array(),
		'helper' => array()
	);

	/**
	* The name of the default template source file.
	*
	* @var string
	*/
	private $_template = null;

	/**
	* The output of the template script.
	*
	* @var string
	*/
	private $_output = null;

	/**
	 * Callback for escaping.
	 *
	 * @var string
	 */
	private $_escape = 'htmlspecialchars';

	/**
	 * Charset to use in escaping mechanisms; defaults to urf8 (UTF-8)
	 *
	 * @var string
	 */
	private $_charset = 'UTF-8';

	/**
	 * Constructor
	 */
	public function __construct($config = array())
	{
		// Guess the option as com_prefixOfView
		if (empty($this->_option)) {
			$this->_option = (isset($config['option'])) ? $config['option'] : 'com_'.$this->getPrefix();
		}
		// set the charset (used by the variable escaping functions)
		if (isset($config['charset'])) {
			$this->_charset = $config['charset'];
		}

		// user-defined escaping callback
		if (isset($config['escape'])) {
			$this->setEscape($config['escape']);
		}

		// Set a base path for use by the view
		if (isset($config['escape'])) {
			$this->_basePath = $config['base_path'];
		} else {
			$this->_basePath = JPATH_COMPONENT;
		}

		// set the default template search path
		if (isset($config['template_path'])) {
			// user-defined dirs
			$this->_setPath('template', $config['template_path']);
		} else {
			$this->_setPath('template', $this->_basePath.'/views/'.$this->getName().'/tmpl');
		}

		// set the default helper search path
		if (isset($config['helper_path'])) {
			// user-defined dirs
			$this->_setPath('helper', $config['helper_path']);
		} else {
			$this->_setPath('helper', $this->_basePath.'/helpers');
		}

		// set the layout
		if (isset($config['layout'])) {
			$this->setLayout($config['layout']);
		} else {
			$this->setLayout('default');
		}

		$this->baseurl = JURI::base(true);
	}

	/**
	* Execute and display a template script.
	*
	* @param string $tpl	The name of the template file to parse;
	* automatically searches through the template paths.
	*
	* @throws object An JError object.
	* @see fetch()
	*/
	public function display($tpl = null)
	{
		$result = $this->loadTemplate($tpl);
		if (JError::isError($result)) {
			return $result;
		}

		echo $result;
	}
	
	protected function addSubMenu()
	{
		$this->loadHelper($this->getName());
		$class = ucfirst($this->getPrefix()).'Helper';
		if (class_exists($class)) {
			return call_user_func(array($class,'addSubmenu'), $this->getName());
		}
		else {
			return false;
		}
	}

	/**
	 * Escapes a value for output in a view script.
	 *
	 * If escaping mechanism is one of htmlspecialchars or htmlentities, uses
	 * {@link $_encoding} setting.
	 *
	 * @param  mixed $var	The output to escape.
	 * @return mixed The escaped value.
	 */
	protected function escape($var)
	{
		if (in_array($this->_escape, array('htmlspecialchars', 'htmlentities'))) {
			return call_user_func($this->_escape, $var, ENT_COMPAT, $this->_charset);
		}

		return call_user_func($this->_escape, $var);
	}

	/**
	 * Method to get data from a registered model or a property of the view
	 *
	 * @param	string	$property	The name of the method to call on the model, or the property to get
	 * @param	string	$default	The name of the model to reference, or the default value [optional]
	 * @return	mixed	The return value of the method
	 */
	public function get($property, $default = null)
	{

		// If $model is null we use the default model
		if (is_null($default)) {
			$model = $this->_defaultModel;
		} else {
			$model = strtolower($default);
		}

		// First check to make sure the model requested exists
		if (isset($this->_models[$model]))
		{
			// Model exists, lets build the method name
			$method = 'get'.ucfirst($property);

			// Does the method exist?
			if (method_exists($this->_models[$model], $method))
			{
				// The method exists, lets call it and return what we get
				$result = $this->_models[$model]->$method();
				return $result;
			}

		}

		// degrade to JObject::get
		$result = parent::get($property, $default);
		return $result;

	}

	/**
	 * Method to get the model object
	 *
	 * @param	string	The name of the model (optional)
	 * @return	mixed	JModel object
	 */
	public function getModel($name = null)
	{
		if ($name === null) {
			$name = $this->_defaultModel;
		}
		return $this->_models[strtolower($name)];
	}

	/**
	* Get the layout.
	*
	* @return	string	The layout name
	*/
	public function getLayout()
	{
		return $this->_layout;
	}
	
	/**
	* Get the layout template.
	*
	* @return	string	The layout template name
	*/
	public function getLayoutTemplate()
	{
		return $this->_layoutTemplate;
	}

	/**
	 * Method to get the view name
	 *
	 * The dispatcher name by default parsed using the classname, or it can be set
	 * by passing a $config['name'] in the class constructor
	 *
	 * @return	string The name of the controller
	 */
	public function getName()
	{
		if (empty($this->_name))
		{
			if (!preg_match('/View([a-z]*)$/i', get_class($this), $r)) {
				JError::raiseError(500, JText::_('JLIB_APPLICATION_ERROR_VIEW_GET_NAME'));
			}
			$this->_name = strtolower($r[1]);
		}

		return $this->_name;
	}
	
	/**
	 * Method to get the view prefix
	 *
	 * The controller name by default parsed using the classname, or it can be set
	 * by passing a $config['prefix'] in the class constructor
	 *
	 * @return	string
	 */
	public function getPrefix()
	{
		if (empty($this->_prefix))
		{
			if (!preg_match('/^([a-z]*)View/i', get_class($this), $r)) {
				JError::raiseError(500, JText::_('JGLIB_APPLICATION_ERROR_VIEW_GET_PREFIX'));
			}
			$this->_prefix = strtolower($r[1]);
		}

		return $this->_prefix;
	}

	/**
	 * Method to add a model to the view.  We support a multiple model single
	 * view system by which models are referenced by classname.  A caveat to the
	 * classname referencing is that any classname prepended by JModel will be
	 * referenced by the name without JModel, eg. JModelCategory is just
	 * Category.
	 *
	 * @param	object	$model		The model to add to the view.
	 * @param	boolean	$default	Is this the default model?
	 * @return	object	The added model
	 */
	public function setModel(&$model, $default = false)
	{
		$name = strtolower($model->getName());
		$this->_models[$name] = &$model;

		if ($default) {
			$this->_defaultModel = $name;
		}
		return $model;
	}

	/**
	* Sets the layout name to use
	*
	* @param	string	$layout	The layout name or a string in format <template>:<layout file>
	* @return	string 	Previous value
	*/
	public function setLayout($layout)
	{
		$previous = $this->_layout;
		if (strpos($layout, ':') === false )
		{
			$this->_layout = $layout;
		}
		else
		{
			// Convert parameter to array based on :
			$temp = explode(':', $layout);
			$this->_layout = $temp[1];
			// Set layout template
			$this->_layoutTemplate = $temp[0];
		}
		return $previous;
	}

	/**
	 * Allows a different extension for the layout files to be used
	 *
	 * @param	string	$value	The extension
	 * @return	string	Previous value
	 */
	public function setLayoutExt($value)
	{
		$previous = $this->_layoutExt;
		if ($value = preg_replace('#[^A-Za-z0-9]#', '', trim($value))) {
			$this->_layoutExt = $value;
		}
		return $previous;
	}

	/**
	 * Sets the _escape() callback.
	 *
	 * @param	mixed	$spec	The callback for _escape() to use.
	 */
	public function setEscape($spec)
	{
		$this->_escape = $spec;
	}

	/**
	 * Adds to the stack of view script paths in LIFO order.
	 *
	 * @param	string|array $path	The directory (-ies) to add.
	 * @return	void
	 */
	public function addTemplatePath($path)
	{
		$this->_addPath('template', $path);
	}

	/**
	 * Adds to the stack of helper script paths in LIFO order.
	 *
	 * @param	string|array $path The directory (-ies) to add.
	 * @return	void
	 */
	public function addHelperPath($path)
	{
		$this->_addPath('helper', $path);
	}

	/**
	 * Load a template file -- first look in the templates folder for an override
	 *
	 * @param	string $tpl	The name of the template source file ...
	 * automatically searches the template paths and compiles as needed.
	 * @return	string The output of the the template script.
	 */
	public function loadTemplate($tpl = null)
	{
		// clear prior output
		$this->_output = null;

		$template = JFactory::getApplication()->getTemplate();
		$layout = $this->getLayout();
		$layoutTemplate = $this->getLayoutTemplate();

		//create the template file name based on the layout
		$file = isset($tpl) ? $layout.'_'.$tpl : $layout;
		// clean the file name
		$file = preg_replace('/[^A-Z0-9_\.-]/i', '', $file);
		$tpl  = isset($tpl)? preg_replace('/[^A-Z0-9_\.-]/i', '', $tpl) : $tpl;

		// Load the language file for the template
		$lang	= JFactory::getLanguage();
			$lang->load('tpl_'.$template, JPATH_BASE, null, false, false)
		||	$lang->load('tpl_'.$template, JPATH_THEMES."/$template", null, false, false)
		||	$lang->load('tpl_'.$template, JPATH_BASE, $lang->getDefault(), false, false)
		||	$lang->load('tpl_'.$template, JPATH_THEMES."/$template", $lang->getDefault(), false, false);
		
		// change the template folder if alternative layout is in different template
		if (isset($layoutTemplate) && $layoutTemplate != '_' && $layoutTemplate != $template)
		{
			$this->_path['template'] = str_replace($template, $layoutTemplate, $this->_path['template']);
		}

		// load the template script
		jimport('joomla.filesystem.path');
		$filetofind	= $this->_createFileName('template', array('name' => $file));
		$this->_template = JPath::find($this->_path['template'], $filetofind);
		
		// If alternate layout can't be found, fall back to default layout
		if ($this->_template == false) 
		{
			$filetofind = $this->_createFileName('', array('name' => 'default' . (isset($tpl) ? '_' . $tpl : $tpl)));
			$this->_template = JPath::find($this->_path['template'], $filetofind);
		}

		if ($this->_template != false)
		{
			// unset so as not to introduce into template scope
			unset($tpl);
			unset($file);

			// never allow a 'this' property
			if (isset($this->this)) {
				unset($this->this);
			}

			// start capturing output into a buffer
			ob_start();
			// include the requested template filename in the local scope
			// (this will execute the view logic).
			include $this->_template;

			// done with the requested template; get the buffer and
			// clear it.
			$this->_output = ob_get_contents();
			ob_end_clean();

			return $this->_output;
		}
		else {
			return JError::raiseError(500, JText::sprintf('JLIB_APPLICATION_ERROR_LAYOUTFILE_NOT_FOUND', $file));
		}
	}

	/**
	 * Load a helper file
	 *
	 * @param	string	$hlp	The name of the helper source file ...
	 * automatically searches the helper paths and compiles as needed.
	 * @return	void
	 */
	public function loadHelper($hlp = null)
	{
		// clean the file name
		$file = preg_replace('/[^A-Z0-9_\.-]/i', '', $hlp);

		// load the template script
		jimport('joomla.filesystem.path');
		$helper = JPath::find($this->_path['helper'], $this->_createFileName('helper', array('name' => $file)));

		if ($helper != false)
		{
			// include the requested template filename in the local scope
			include_once $helper;
		}
	}

	/**
	 * Sets an entire array of search paths for templates or resources.
	 *
	 * @param	string			The type of path to set, typically 'template'.
	 * @param	string|array	The new set of search paths.  If null or false, resets to the current directory only.
	 * @return	void
	 */
	protected function _setPath($type, $path)
	{
		jimport('joomla.application.helper');
		$app		= JFactory::getApplication();

		// clear out the prior search dirs
		$this->_path[$type] = array();

		// actually add the user-specified directories
		$this->_addPath($type, $path);

		// always add the fallback directories as last resort
		switch (strtolower($type))
		{
			case 'template':
				// Set the alternative template search dir
				if (isset($app))
				{
					$fallback	= JPATH_THEMES.'/'.$app->getTemplate().'/html/'.$this->_option.'/'.$this->getName();
					$this->_addPath('template', $fallback);
				}
				break;
		}
	}

	/**
	 * Adds to the search path for templates and resources.
	 *
	 * @param	string 			The type of path to add to.
	 * @param	string|array	The directory or stream to search.
	 * @return	void
	 */
	protected function _addPath($type, $path)
	{
		// just force to array
		$path = (array) $path;

		// loop through the path directories
		foreach ($path as $dir)
		{
			// no surrounding spaces allowed!
			$dir = trim($dir);

			// add trailing separators as needed
			if (substr($dir, -1) != DS) {
				// directory
				$dir .= DS;
			}

			// add to the top of the search dirs
			array_unshift($this->_path[$type], $dir);
		}
	}

	/**
	 * Create the filename for a resource
	 *
	 * @param	string	$type	The resource type to create the filename for
	 * @param	array	$parts	An associative array of filename information
	 * @return	string	The filename
	 */
	private function _createFileName($type, $parts = array())
	{
		$filename = '';

		switch($type)
		{
			case 'template' :
				$filename = strtolower($parts['name']).'.'.$this->_layoutExt;
				break;

			default :
				$filename = strtolower($parts['name']).'.php';
				break;
		}
		return $filename;
	}
}
