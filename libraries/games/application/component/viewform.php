<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Libraries
 * @license		GNU General Public License version 3
 */

// No direct access
defined('_JEXEC') or die;

JGImport('application.component.view');
JGImport('utilities.inflect');

/**
 *
 */
class JGViewForm extends JGView
{
	protected $item;
	protected $form;
	protected $state;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Guess the list controller as the name.
		if (empty($this->_controller_item)) {
			$this->_controller_item = (isset($config['controller_item'])) ? $config['controller_item'] : $this->getName();
		}
	}
	
	public function setUpData()
	{
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
		$this->state	= $this->get('State');
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		JGImport('toolbar.helper');
		JToolBar::getInstance('toolbar')->addButtonPath(JPATH_LIBRARIES.'/games/toolbar/button');
		JHtml::_('script', 'games/toolbar.js', true, true);
		JRequest::setVar('hidemainmenu', true);

		$isNew = ($this->item->id == 0);

		JToolBarHelper::title(JText::_($this->_option.'_PAGE_'.($isNew ? 'ADD' : 'EDIT').'_'.$this->getName()), $this->getName().'-add');

		JGToolBarHelper::apply($this->_controller_item);
		JGToolBarHelper::save($this->_controller_item);
		JGToolBarHelper::save2new($this->_controller_item);

		// If an existing item, can save to a copy.
		if (!$isNew ) {
			JGToolBarHelper::save2copy($this->_controller_item);
			JGToolBarHelper::cancel($this->_controller_item);
		} else {
			JGToolBarHelper::close($this->_controller_item);
		}

		JToolBarHelper::divider();
		JToolBarHelper::help('');
	}
}