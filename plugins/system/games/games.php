<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Plugins
 * @license		GNU General Public License version 3
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * 
 *
 */
class plgSystemGames extends JPlugin
{
	function onAfterInitialise()
	{
		$com_enabled = JComponentHelper::isEnabled('com_games');
		$plg_enabled = JPluginHelper::isEnabled('content', 'games');
		if(!$com_enabled || !$plg_enabled) {
			return;
		}
		require_once JPATH_ADMINISTRATOR.'/components/com_games/loader.php';
		JLoader::import('joomla.application.component.modellist', dirname(__FILE__).'/override');
	}
}