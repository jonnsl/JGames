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
 * Base class for list based operations
 *
 * @since 1.0
 */
class JGControllerList extends JGController
{
	/**
	 * The URL option for the component.
	 *
	 * @var	string
	 */
	protected $option;

	/**
	 * The URL view list variable.
	 *
	 * @var	string
	 */
	protected $view_list;

	/**
	 * The prefix to use with controller messages.
	 *
	 * @var	string
	 */
	protected $text_prefix;

	/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 * @see		JController
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Guess the option as the prefix.
		if (empty($this->option)) {
			$this->option = 'com_'.$this->getPrefix();
		}

		// Guess the list view as the content.
		if (empty($this->view_list)) {
			$this->view_list = $this->getName();
		}

		// Set the Message prefix
		if (empty($this->text_prefix)) {
			$this->text_prefix = strtoupper($this->option.'_'.$this->getName().'_');
		}

		// Setup redirect info.
		$this->_append		= '';

		if ($tmpl = JRequest::getString('tmpl')) {
			$this->_append .= '&tmpl='.$tmpl;
		}
		if ($layout = JRequest::getString('layout')) {
			$this->_append .= '&layout='.$layout;
		}

		// Unpublish, Archive, Trash, Orderup and Orderdown should be standard on lists.
		$this->registerTask('unpublish',	'publish');
		$this->registerTask('archive',		'publish');
		$this->registerTask('trash',		'publish');
		$this->registerTask('orderup',		'reorder');
		$this->registerTask('orderdown',	'reorder');
	}

	/**
	 * Method to check if you can delete a record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param	integer	The primary key value.
	 * @return	boolean
	 */
	protected function allowDelete($pk)
	{
		return JFactory::getUser()->authorise('core.delete', $this->option);
	}

	/**
	 * Method to delete a list of records.
	 */
	public function delete()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$ids	= JRequest::getVar('cid', array(), 'post', 'array');

		// Sanitize the ids
		JArrayHelper::toInteger($ids);

		// Access checks.
		foreach ($ids as $i => $id)
		{
			if (!$this->allowDelete($id))
			{
				// Prune items that you can't delete.
				unset($ids[$i]);
				JError::raiseNotice(403, JText::sprintf($this->text_prefix.'ERROR_DELETE_N_NOT_PERMITTED', $id));
			}
		}

		if (empty($ids)) {
			JError::raiseWarning(500, JText::_($this->text_prefix.'ERROR_NO_ITEMS_SELECTED'));
		}
		else
		{
			// Get the model.
			$model		= $this->getModel();
			$success	= array();

			foreach ($ids as $id)
			{
				// Remove the item.
				if (!$model->delete($id)) {
					JError::raiseWarning(500, $model->getError());
				}
				else {
					// Set as successfully deleted
					$success[] = $id;
				}
			}
			if (count($success)) {
				$this->setMessage(JText::plural($this->text_prefix.'SUCCESS_N_ITEMS_DELETED', count($success)));
			}
		}

		$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list.$this->_append, false));
	}

	/**
	 * Method to check if you can edit the state of a record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param	integer	The primary key value.
	 *
	 * @return	boolean
	 */
	protected function allowEditState($pk)
	{
		return JFactory::getUser()->authorise('core.edit.state', $this->option);
	}

	/**
	 * Method to change the state of a list of records.
	 */
	public function publish()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$ids	= JRequest::getVar('cid', array(), 'post', 'array');
		$values	= array('publish' => 1, 'unpublish' => 0, 'archive' => -1, 'trash' => -2);
		$task	= JArrayHelper::getValue($values, $this->getTask(), 0, 'int');

		// Access checks.
		foreach ($ids as $i => $id)
		{
			if (!$this->allowEditState($id))
			{
				// Prune items that you can't delete.
				unset($ids[$i]);
				JError::raiseNotice(403, JText::sprintf($this->text_prefix.'ERROR_EDIT_STATE_N_NOT_PERMITTED', $id));
			}
		}

		if (empty($ids)) {
			JError::raiseWarning(500, JText::_($this->text_prefix.'ERROR_NO_ITEMS_SELECTED'));
		}
		else
		{
			// Get the model.
			$model	= $this->getModel();

			// Publish the items.
			if (!$model->publish($ids, $task)) {
				JError::raiseWarning(500, $model->getError());
			}
			else
			{
				$text = $this->text_prefix.'SUCCESS_N_ITEMS_';
				switch($task)
				{
					case 0:
						$text .= 'UNPUBLISHED';
						break;
					case 1:
						$text .= 'PUBLISHED';
						break;
					case 2:
						$text .= 'ARCHIVED';
						break;
					default:
						$text .= 'TRASHED';
						break;
				}

				$this->setMessage(JText::plural($text, count($ids)));
			}
		}

		$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list.$this->_append, false));
	}

	/**
	 * Method to check if you can add a new record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param	integer	The primary key.
	 * @return	boolean
	 */
	protected function allowEdit($pk)
	{
		return JFactory::getUser()->authorise('core.edit', $this->option);
	}

	/**
	 * Method to move a record.
	 */
	public function reorder()
	{
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$id		= (int) reset(JRequest::getVar('cid', array(), 'post', 'array'));
		$dir	= ($this->getTask() == 'orderup') ? -1 : 1;

		// Check for a id
		if($id)
		{
			// Access check
			if ($this->allowEdit($id))
			{
				// Move
				$model	= $this->getModel();

				if($model->reorder($id, $dir)) {
					$this->setMessage(JText::_($this->text_prefix.'SUCCESS_ITEM_REOEDERED'));
				}
				else {
					JError::raiseWarning(500, JText::sprintf($this->text_prefix.'ERROR_REORDER_FAILED', $model->getError()));
				}
			}
			else {
				JError::raiseNotice(403, JText::sprintf($this->text_prefix.'ERROR_EDIT_N_NOT_PERMITTED', $id));
			}

		}
		else {
			JError::raiseWarning(500, JText::_($this->text_prefix.'ERROR_NO_ITEMS_SELECTED'));
		}

		$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list.$this->_append, false));
	}

	/**
	 * Method to change the order of a list of records.
	 */
	public function saveorder()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Get the input
		$ids	= JRequest::getVar('cid',	array(), 'post', 'array');
		$order	= JRequest::getVar('order',	array(), 'post', 'array');

		// Sanitize the input
		JArrayHelper::toInteger($ids);
		JArrayHelper::toInteger($order);

		// Access checks.
		foreach ($ids as $i => $id)
		{
			if (!$this->allowEdit($id))
			{
				// Prune items that you can't delete.
				unset($ids[$i]);
				JError::raiseNotice(403, JText::sprintf($this->text_prefix.'ERROR_EDIT_N_NOT_PERMITTED', $id));
			}
		}

		if (empty($ids)) {
			JError::raiseWarning(500, JText::_($this->text_prefix.'ERROR_NO_ITEMS_SELECTED'));
		}
		else
		{
			// Get the model
			$model = $this->getModel();

			// Save the ordering
			if($model->saveorder($ids, $order)) {
				$this->setMessage(JText::_($this->text_prefix.'SUCCESS_ORDERING_SAVED'));
			}
			else{
				JError::raiseWarning(500, JText::sprintf($this->text_prefix.'ERROR_SAVEORDER_FAILED', $model->getError()));
			}
		}

		$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list.$this->_append, false));
	}
}