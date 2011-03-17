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
class GamesControllerForm extends GamesController
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
	 * The URL view item variable
	 *
	 * @var	string
	 */
	protected $view_item;

	/**
	 * The URL view list variable
	 *
	 * @var	string
	 */
	protected $view_list;

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
		parent::__construct($config);

		// Guess the option as com_NameOfController
		if (empty($this->option)) {
			$this->option = 'com_'.$this->getPrefix();
		}

		// Guess the JText message prefix. Defaults to the option.
		if (empty($this->text_prefix)) {
			$this->text_prefix = strtoupper($this->option);
		}

		// Guess the context as the suffix, eg: OptionControllerContent.
		if (empty($this->context)) {
			$this->context = $this->getName();
		}

		// Guess the item view as the context.
		if (empty($this->view_item)) {
			$this->view_item = $this->context;
		}

		// Guess the list view as the plural of the item view.
		if (empty($this->view_list)) {
			$this->view_list = Inflect::pluralize($this->view_item);
		}

		// Setup redirect info.
		if ($tmpl = JRequest::getString('tmpl')) {
			$this->append .= '&tmpl='.$tmpl;
		}
		if ($layout = JRequest::getString('layout', 'edit')) {
			$this->append .= '&layout='.$layout;
		}

		// Apply, Save & New, and Save As copy should be standard on forms.
		$this->registerTask('apply',		'save');
		$this->registerTask('save2new',		'save');
		$this->registerTask('save2copy',	'save');
	}

	/**
	 * Method to add a new record.
	 */
	public function add()
	{
		// Initialise variables.
		$app		= JFactory::getApplication();
		$context	= $this->option.'.edit.'.$this->context;

		// Access check.
		if (!$this->allowAdd()) {
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
			return JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'));
		}

		// Clear the record edit information from the session.
		$app->setUserState($context.'.id', null);
		$app->setUserState($context.'.data', null);

		// Redirect to the edit screen.
		$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_item.$this->append, false));
	}

	/**
	 * Method to check if you can add a new record.
	 *
	 * @param	array	An array of input data.
	 * @return	boolean
	 */
	protected function allowAdd($data = array())
	{
		return JFactory::getUser()->authorise('core.create', $this->option);
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
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app		= JFactory::getApplication();
		$model		= $this->getModel();
		$table		= $model->getTable();
		$checkin	= property_exists($table, 'checked_out');
		$context	= $this->option.'.edit.'.$this->context;

		// Get the record id.
		$recordId = (int) $app->getUserState($context.'.id');

		// Attempt to check-in the current record.
		if ($checkin && $recordId) {
			if(!$model->checkin($recordId)) {
				// Check-in failed, go back to the record and display a notice.
				$message = JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError());
				$this->setRedirect('index.php?option='.$this->option.'&view='.$this->view_item.$this->append, $message, 'error');
				return false;
			}
		}

		// Clean the session data and redirect.
		$app->setUserState($context.'.id',		null);
		$app->setUserState($context.'.data',	null);
		$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
	}

	/**
	 * Method to edit an existing record.
	 */
	public function edit()
	{
		// Initialise variables.
		$app		= JFactory::getApplication();
		$model		= $this->getModel();
		$table		= $model->getTable();
		$cid		= JRequest::getVar('cid', array(), 'post', 'array');
		$context	= $this->option.'.edit.'.$this->context;

		// Get the previous record id (if any) and the current record id.
		$previousId	= (int) $app->getUserState($context.'.id');
		$recordId	= (int) (count($cid) ? reset($cid) : JRequest::getInt('id'));
		$checkin	= property_exists($table, 'checked_out');

		// Access check.
		$key		= $table->getKeyName();
		if (!$this->allowEdit(array($key => $recordId), $key)) {
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
			return JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
		}

		// If record ids do not match, checkin previous record.
		if ($checkin && ($previousId > 0) && ($recordId != $previousId)) {
			if (!$model->checkin($previousId)) {
				// Check-in failed, go back to the record and display a notice.
				$message = JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError());
				$this->setRedirect('index.php?option='.$this->option.'&view='.$this->view_item.$this->append, $message, 'error');
				return false;
			}
		}

		// Attempt to check-out the new record for editing and redirect.
		if ($checkin && !$model->checkout($recordId)) {
			// Check-out failed, go back to the list and display a notice.
			$message = JText::sprintf('JLIB_APPLICATION_ERROR_CHECKOUT_FAILED', $model->getError());
			$this->setRedirect('index.php?option='.$this->option.'&view='.$this->view_item.$this->append.'&id='.$recordId, $message, 'error');
			return false;
		} else {
			// Check-out succeeded, push the new record id into the session.
			$app->setUserState($context.'.id',	$recordId);
			$app->setUserState($context.'.data', null);
			$this->setRedirect('index.php?option='.$this->option.'&view='.$this->view_item.$this->append);
			return true;
		}
	}

	/**
	 * Method to save a record.
	 */
	public function save()
	{
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
		$recordId	= (int) $app->getUserState($context.'.id');

		// Populate the row id from the session.
		$key		= $table->getKeyName();
		$data[$key] = $recordId;

		// The save2copy task needs to be handled slightly differently.
		if ($task == 'save2copy') {
			// Check-in the original row.
			if ($checkin  && !$model->checkin($data[$key])) {
				// Check-in failed, go back to the item and display a notice.
				$message = JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError());
				$this->setRedirect('index.php?option='.$this->option.'&view='.$this->view_item.$this->append, $message, 'error');
				return false;
			}

			// Reset the primary key and then treat the request as for Apply.
			$data[$key]	= 0;
			$task		= 'apply';
		}

		// Access check.
		if (!$this->allowSave($data)) {
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
			return JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
		}

		// Validate the posted data.
		$data = $model->validate($data);

		// Check for validation errors.
		if ($data === false) {
			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
				if (JError::isError($errors[$i])) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'notice');
				} else {
					$app->enqueueMessage($errors[$i], 'notice');
				}
			}

			// Save the data in the session.
			$app->setUserState($context.'.data', $data);

			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_item.$this->append, false));
			return false;
		}

		// Attempt to save the data.
		if (!$model->save($data)) {
			// Save the data in the session.
			$app->setUserState($context.'.data', $data);

			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'notice');
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_item.$this->append, false));
			return false;
		}

		// Save succeeded, check-in the record.
		if ($checkin && !$model->checkin($data[$key])) {
			// Save the data in the session.
			$app->setUserState($context.'.data', $data);

			// Check-in failed, go back to the record and display a notice.
			$message = JText::sprintf('JERROR_CHECKIN_SAVED', $model->getError());
			$this->setRedirect('index.php?option='.$this->option.'&view='.$this->view_item.$this->append, $message, 'error');
			return false;
		}

		$this->setMessage(JText::_($this->text_prefix.'_SAVE_SUCCESS'));

		// Redirect the user and adjust session state based on the chosen task.
		switch ($task) {
			case 'apply':
				// Set the record data in the session.
				$app->setUserState($context.'.id',		$model->getState($this->context.'.id'));
				$app->setUserState($context.'.data',	null);

				// Redirect back to the edit screen.
				$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_item.$this->append, false));
				break;

			case 'save2new':
				// Clear the record id and data from the session.
				$app->setUserState($context.'.id', null);
				$app->setUserState($context.'.data', null);

				// Redirect back to the edit screen.
				$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_item.$this->append, false));
				break;

			default:
				// Clear the record id and data from the session.
				$app->setUserState($context.'.id', null);
				$app->setUserState($context.'.data', null);

				// Redirect to the list screen.
				$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
				break;
		}

		return true;
	}
}