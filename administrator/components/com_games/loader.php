<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Libraries
 * @license		GNU General Public License version 3
 */

// No direct access
defined('_JEXEC') or die;

// TODO Substitute the games prefix by a capital letter

// Base classes
JLoader::register('GamesController', dirname(__FILE__).'/libraries/application/component/controller.php');
JLoader::register('GamesControllerForm', dirname(__FILE__).'/libraries/application/component/controllerform.php');
JLoader::register('GamesControllerList', dirname(__FILE__).'/libraries/application/component/controllerlist.php');
JLoader::register('GamesModel', dirname(__FILE__).'/libraries/application/component/model.php');
JLoader::register('GamesModelForm', dirname(__FILE__).'/libraries/application/component/modelform.php');
JLoader::register('GamesModelList', dirname(__FILE__).'/libraries/application/component/modellist.php');
JLoader::register('JView', JPATH_LIBRARIES.'/joomla/application/component/view.php');

// Form classes
//JLoader::register('GamesForm', dirname(__FILE__).'/libraries/form/form.php');

// Helpers
JLoader::register('GamesHelper', dirname(__FILE__).'/helpers/games.php');

// Tables
JLoader::register('GamesTable', dirname(__FILE__).'/libraries/database/table.php');
JLoader::register('JTable', JPATH_LIBRARIES.'/joomla/database/table.php');

// Utilities
JLoader::register('Inflect', dirname(__FILE__).'/libraries/utilities/inflect.php');
JLoader::register('GamesArrayHelper', dirname(__FILE__).'/libraries/utilities/arrayhelper.php');