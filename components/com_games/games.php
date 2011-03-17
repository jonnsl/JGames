<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Site
 * @license		GNU General Public License version 3
 */

// No direct access
defined('_JEXEC') or die;

// Include dependencies
require_once(JPATH_COMPONENT_ADMINISTRATOR.'/loader.php');

$controller = GamesController::getInstance('Games');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();