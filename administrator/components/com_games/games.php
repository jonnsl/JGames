<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Administrator
 * @license		GNU General Public License version 3
 */

// No direct access
defined('_JEXEC') or die;

// Check Plugin.
if (!JPluginHelper::isEnabled('system', 'games')) {
	return JError::raiseWarning(404, JText::_('COM_GAMES_ERROR_PLUGIN_SYSTEM_DISABLED'));
}

// Include dependencies
JGImport('application.component.controller');

$controller = JGController::getInstance('Games');
$controller->execute();
$controller->redirect();