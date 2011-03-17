<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Administrator
 * @license		GNU General Public License version 3
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.form.formrule');

/**
 *
 */
class JFormRulePositive extends JFormRule
{
	/**
	 * The regular expression to use in testing a form field value.
	 *
	 * @var		string
	 */
	protected $regex = '^[0-9]*$';
}