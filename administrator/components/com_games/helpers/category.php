<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Administrator
 * @license		GNU General Public License version 3
 */

// No direct access
defined('_JEXEC') or die;

class GamesPlatformsHelperCategory
{

	public static function onPrepareForm($form)	{
		$form->removeField('parent_id');
	}
}