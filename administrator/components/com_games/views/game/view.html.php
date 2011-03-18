<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Administrator
 * @license		GNU General Public License version 3
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
require dirname(__FILE__).'/../../helpers/games.php';

/**
 *
 */
class GamesViewGame extends JView
{
	protected $item;
	protected $form;
	protected $state;

	public function display($tpl = null)
	{
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
		$this->state	= $this->get('State');
		$this->platformsParams	=  $this->get('PlatformsParams');

		GamesHelper::addSubmenu($this->getName());
		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);

		JToolBarHelper::title(JText::_('COM_GAMES_PAGE_'.($isNew ? 'ADD' : 'EDIT').'_GAME'), 'game-add');

		JToolBarHelper::apply('apply', 'JTOOLBAR_APPLY');
		JToolBarHelper::save('save', 'JTOOLBAR_SAVE');
		JToolBarHelper::custom('save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);

		// If an existing item, can save to a copy.
		if (!$isNew ) {
			JToolBarHelper::custom('save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
			JToolBarHelper::cancel('cancel', 'JTOOLBAR_CANCEL');
		} else {
			JToolBarHelper::cancel('cancel', 'JTOOLBAR_CLOSE');
		}

		JToolBarHelper::divider();
		JToolBarHelper::help('');
	}
}