<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Site
 * @license		GNU General Public License version 3
 */

// No direct access
defined('_JEXEC') or die;

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
		$this->params		= $this->state->params;
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->series		= $this->get('Series');
		$this->developers	= $this->get('Developers');
		$this->publishers	= $this->get('Publishers');
		$this->orders		= $this->get('OrdersOptions');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$app		= JFactory::getApplication();
		$menu		= $app->getMenu()->getActive();

		if ($menu) {
			$this->params->def('page_heading', $menu->title);
		}
		else {
			$this->params->def('page_heading', JText::_('JGAMES_GAMES'));
		}

		parent::display($tpl);
	}
}