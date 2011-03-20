<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Administrator
 * @license		GNU General Public License version 3
 */

// No direct access
defined('_JEXEC') or die;

JGImport('application.component.viewList');

/**
 *
 */
class GamesViewGames extends JGViewList
{
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->series		= $this->get('Series');
		$this->developers	= $this->get('Developers');
		$this->publishers	= $this->get('Publishers');

		$this->addToolbar();
		$this->addSubmenu();
		parent::display($tpl);
	}
}