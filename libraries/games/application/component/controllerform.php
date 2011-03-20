<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Libraries
 * @license		GNU General Public License version 3
 */

// No direct access
defined('_JEXEC') or die;

JGImport('application.component.controller');
JGImport('utilities.inflect');
/**
 *
 */
class JGControllerForm extends JGController
{
	/**
	 * Redirect info
	 *
	 * @var	string
	 */
	protected $append = '';

	/**
	 * The context for storing internal data, eg record
	 *
	 * @var	string
	 */
	protected $context;

	/**
	 * The URL option for the component
	 *
	 * @var	string
	 */
	protected $option;

	/**
	 * The controller item
	 *
	 * @var	string
	 */
	protected $controller_item;

	/**
	 * The controller list
	 *
	 * @var	string
	 */
	protected $controller_list;

	/**
	 * The prefix to use with controller messages
	 *
	 * @var	string
	 */
	protected $text_prefix;

	/**
	 * Constructor.
	 */
	public function __construct($config = array())
	{
		// The default task for a form is edot
		if (!isset($config['default_task'])) {
			$config['default_task'] = 'edit';
		}

		parent::__construct($config);

		// A form has no task display
		$this->unregisterTask('display');

		// Guess the option as com_prefixOfController
		if (empty($this->option)) {
			$this->option = (isset($config['option'])) ? $config['option'] : 'com_'.$this->getPrefix();
		}

		// Guess the JText message prefix. Defaults to the option.
		if (empty($this->text_prefix)) {
			$this->text_prefix = (isset($config['text_prefix'])) ? $config['text_prefix'] : strtoupper($this->option);
		}

		// Guess the context as the suffix, eg: OptionControllerContent.
		if (empty($this->context)) {
			$this->context = (isset($config['context'])) ? $config['context'] : $this->getName();
		}

		// Guess the item view as the context.
		if (empty($this->controller_item)) {
			$this->controller_item = (isset($config['controller_item'])) ? $config['controller_item'] : $this->context;
		}

		// Guess the list view as the plural of the item view.
		if (empty($this->controller_list)) {
			$this->controller_list = (isset($config['controller_list'])) ? $config['controller_list'] : JGInflect::pluralize($this->controller_item);
		}

		// Define default layout.
		$layout = JRequest::getCmd('layout', 'edit');
		JRequest::setVar('layout', $layout);

		// Apply, Save & New, and Save As copy should be standard on forms.
		$this->registerTask('apply',		'save');
		$this->registerTask('save2new',		'save');
		$this->registerTask('save2copy',	'save');
	}

	/**
	 * Method to add a new record.
	 * 
	 * @return	mixed	True if the record can be added, false if not.
	 */
	public function add()
	{
		JError::raiseWarning(0, 'verifying permissions for add');
		// Initialise variables.
		$app		= JFactory::getApplication();
		$context	= $this->option.'.edit.'.$this->context;

		// Access check.
		if (!$this->allowAdd())
		{
			// Set the internal error and also the redirect error.
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&controller='.$this->controller_list, false));

			return false;
		}

		// Clear the record edit information from the session.
		$app->setUserState($context.'.id', null);
		$app->setUserState($context.'.data', null);

		return $this->display();
	}

	/**
	 * Method to check if you can add a new record.
	 *
	 * @param	array	An array of input data.
	 * @return	boolean
	 */
	protected function allowAdd($data = array())
	{
		$user = JFactory::getUser();
		return ($user->authorise('core.create', $this->option) ||
			count($user->getAuthorisedCategories($this->option, 'core.create')));
	}

	/**
	 * Method to check if you can add a new record.
	 *
	 * @param	array	An array of input data.
	 * @param	string	The name of the key for the primary key.
	 * @return	boolean
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		return JFactory::getUser()->authorise('core.edit', $this->option);
	}

	/**
	 * Method to check if you can save a new or existing record.
	 *
	 * @param	array	An array of input data.
	 * @param	string	The name of the key for the primary key.
	 * @return	boolean
	 */
	protected function allowSave($data, $key = 'id')
	{
		// Initialise variables.
		$recordId	= isset($data[$key]) ? $data[$key] : '0';

		if ($recordId) {
			return $this->allowEdit($data, $key);
		} else {
			return $this->allowAdd($data);
		}
	}

	/**
	 * Method to cancel an edit
	 */
	public function cancel()
	{
		JError::raiseWarning(0, 'cancelling');
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app		= JFactory::getApplication();
		$model		= $this->getModel();
		$table		= $model->getTable();
		$checkin	= property_exists($table, 'checked_out');
		$context	= $this->option.'.edit.'.$this->context;
		$key		= $table->getKeyName();
		$recordId	= JRequest::getInt($key);

		// Attempt to check-in the current record.
		if ($recordId && $checkin && $model->checkin($recordId) === false)
		{
			// Check-in failed, go back to the record and display a notice.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
			$this->setMessage($this->getError(), 'error');
			return $this->display();
		}

		// Clean the session data and redirect.
		$app->setUserState($context.'.id', null);
		$app->setUserState($context.'.data', null);
		$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&controller='.$this->controller_list, false));

		return $this;
	}

	/**
	 * Method to edit an existing record.
	 * 
	 * @return Object This object.
	 */
	public function edit()
	{
		JError::raiseWarning(0, 'verifying permissions for editing');
		// Initialise variables.
		$app		= JFactory::getApplication();
		$model		= $this->getModel();
		$table		= $model->getTable();
		$cid		= JRequest::getVar('cid', array(), 'post', 'array');
		$context	= $this->option.'.edit.'.$this->context;
		$key		= $table->getKeyName();

		// Get the previous record id (if any) and the current record id.
		$recordId	= (int) (count($cid) ? $cid[0] : JRequest::getInt($key));
		$checkin	= property_exists($table, 'checked_out');

		// Access check.
		if (!$this->allowEdit(array($key => $recordId), $key))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&controller='.$this->controller_list, false));
			return $this;
		}

		// Attempt to check-out the new record for editing and redirect.
		if ($checkin && !$model->checkout($recordId))
		{
			// Check-out failed, display a notice but allow the user to see the record.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKOUT_FAILED', $model->getError()));
			$this->setMessage($this->getError(), 'error');
		}

		// Push the new record id into the session.
		$app->setUserState($context.'.id', $recordId);
		$app->setUserState($context.'.data', null);
		return $this->display();
	}

	/**
	 * Method to save a record.
	 * 
	 * @return Object This object.
	 */
	public function save()
	{
		JError::raiseWarning(0, 'verifying permissions for save');
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app		= JFactory::getApplication();
		$lang		= JFactory::getLanguage();
		$model		= $this->getModel();
		$table		= $model->getTable();
		$data		= JRequest::getVar('jform', array(), 'post', 'array');
		$checkin	= property_exists($table, 'checked_out');
		$context	= $this->option.'.edit.'.$this->context;
		$task		= $this->getTask();
		$key		= $table->getKeyName();
		$recordId	= JRequest::getInt($key);

		// Populate the row id from the session.
		$data[$key] = $recordId;

		// The save2copy task needs to be handled slightly differently.
		if ($task == 'save2copy')
		{
			// Check-in the original row.
			if ($checkin  && $model->checkin($data[$key]) === false) {
				// Check-in failed, go back to the item and display a notice.
				$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
				$this->setMessage($this->getError(), 'error');
				return $this->display();
			}

			// Reset the ID.
			$data[$key]	= 0;
		}

		// Access check.
		if (!$this->allowSave($data))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&controller='.$this->controller_list, false));

			return $this;
		}

		// Validate the posted data.
		// Sometimes the form needs some posted data, such as for plugins and modules.
		$form = $model->getForm($data, false);

		if (!$form) {
			$app->enqueueMessage($model->getError(), 'error');

			return $this;
		}

		// Test if the data is valid.
		$validData = $model->validate($form, $data);

		// Check for validation errors.
		if ($validData === false)
		{
			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if (JError::isError($errors[$i])) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else {
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState($context.'.data', $data);

			return $this->display();
		}
		
		// Attempt to save the data.
		if (!$model->save($validData)) {
			// Save the data in the session.
			$app->setUserState($context.'.data', $validData);

			// Redirect back to the edit screen.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
			$this->setMessage($this->getError(), 'error');
			return $this->display();
		}

		// Save succeeded, check-in the record.
		if ($checkin && $model->checkin($validData[$key]) === false)
		{
			// Save the data in the session.
			$app->setUserState($context.'.data', $validData);

			// Check-in failed, go back to the record and display a notice.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
			$this->setMessage($this->getError(), 'error');
			return $this->display();
		}

		$this->setMessage(JText::_(($lang->hasKey($this->text_prefix.'_SAVE_SUCCESS') ? $this->text_prefix : 'JLIB_APPLICATION').'_SAVE_SUCCESS'));

		// Redirect the user and adjust session state based on the chosen task.
		switch ($task)
		{
			case 'save2copy':
				// Set the record data in the session.
				$app->setUserState($context.'.data', null);
				return $this->display();
			case 'apply':
				// Set the record data in the session.
				$app->setUserState($context.'.id', $recordId);
				$app->setUserState($context.'.data', null);
				return $this->display();

			case 'save2new':
				// Clear the record id and data from the session.
				$app->setUserState($context.'.id', null);
				$app->setUserState($context.'.data', null);
				return $this->add();

			default:
				// Clear the record id and data from the session.
				$app->setUserState($context.'.id', null);
				$app->setUserState($context.'.data', null);

				// Redirect to the list screen.
				$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&controller='.$this->controller_list, false));
				break;
		}

		return $this;
	}
}