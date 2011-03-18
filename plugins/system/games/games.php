<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Plugins
 * @license		GNU General Public License version 3
 */

// No direct access
defined('_JEXEC') or die;

require JPATH_LIBRARIES.'/games/loader.php';
jimport('joomla.plugin.plugin');

class plgSystemGames extends JPlugin
{
	public function onAfterInitialise()
	{
		$com_enabled = JComponentHelper::isEnabled('com_games');
		$plg_enabled = JPluginHelper::isEnabled('content', 'games');
		if(!$com_enabled || !$plg_enabled) {
			return;
		}
		JLoader::import('joomla.application.component.modellist', dirname(__FILE__).'/override');
	}
}