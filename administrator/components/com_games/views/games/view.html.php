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
class GamesViewGames extends JView
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->series		= $this->get('Series');
		$this->developers	= $this->get('Developers');
		$this->publishers	= $this->get('Publishers');


		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		GamesHelper::addSubmenu($this->getName());
		parent::display($tpl);
	}

	protected function addToolbar()
	{
		JGImport('toolbar.helper');
		JToolBar::getInstance('toolbar')->addButtonPath(JPATH_LIBRARIES.'/games/toolbar/button');
		JHtml::_('script', 'games/toolbar.js', false, true);
		JToolBarHelper::title(JText::_('COM_GAMES_GAMES'), 'games');

		JGToolBarHelper::addNew('game');
		JGToolBarHelper::edit('game');

		JToolBarHelper::divider();
		JGToolBarHelper::publish('games');
		JGToolBarHelper::unpublish('games');

		JToolBarHelper::divider();
		JGToolBarHelper::archive('games');
		//if($this->state->get('filter.state') == -2){
			JGToolBarHelper::delete('games');
		/*}
		else {
			JGToolBarHelper::trash('games');
		}*/

		JToolBarHelper::divider();
		JToolBarHelper::preferences('com_games');

		JToolBarHelper::divider();
		JToolBarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER');
	}
}