<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Administrator
 * @license		GNU General Public License version 3
 */

// No direct access
defined('_JEXEC') or die;

JGImport('application.component.viewForm');


/**
 *
 */
class GamesViewGame extends JGViewForm
{

	public function display($tpl = null)
	{
		$this->platformsParams	=  $this->get('PlatformsParams');

		$this->addSubmenu();
		$this->addToolbar();
		parent::display($tpl);
	}
}