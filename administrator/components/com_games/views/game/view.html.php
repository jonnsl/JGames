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
		JGImport('toolbar.helper');
		JToolBar::getInstance('toolbar')->addButtonPath(JPATH_LIBRARIES.'/games/toolbar/button');
		JHtml::_('script', 'games/toolbar.js', false, true);
		JRequest::setVar('hidemainmenu', true);

		$isNew = ($this->item->id == 0);

		JToolBarHelper::title(JText::_('COM_GAMES_PAGE_'.($isNew ? 'ADD' : 'EDIT').'_GAME'), 'game-add');

		JGToolBarHelper::apply('game');
		JGToolBarHelper::save('game');
		JGToolBarHelper::save2new('game');

		// If an existing item, can save to a copy.
		if (!$isNew ) {
			JGToolBarHelper::save2copy('game');
			JGToolBarHelper::cancel('game');
		} else {
			JGToolBarHelper::close('game');
		}

		JToolBarHelper::divider();
		JToolBarHelper::help('');
	}
}