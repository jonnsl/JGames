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
JGImport('application.component.controller');

$controller = JGController::getInstance('Games');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();