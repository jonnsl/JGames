<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Libraries
 * @license		GNU General Public License version 3
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.html.toolbar');

/**
 *
 */
abstract class JGToolBarHelper
{
	/**
	 * Writes the common 'new' icon for the button bar.
	 *
	 * @param	string	$controller	The Controller to pass the task to.
	 * @param	string	$task		An override for the task.
	 * @param	string	$alt		An override for the alt text.
	 */
	public static function addNew($controller = '', $task = 'add', $alt = 'JTOOLBAR_NEW')
	{
		JToolBar::getInstance('toolbar')
			->appendButton('Standard', 'new', $alt, $task, $controller, false);
	}

	/**
	 * Writes the common 'edit' icon for the button bar.
	 *
	 * @param	string	$controller	The Controller to pass the task to.
	 * @param	string	$task		An override for the task.
	 * @param	string	$alt		An override for the alt text.
	 */
	public static function edit($controller = '', $task = 'edit', $alt = 'JTOOLBAR_EDIT')
	{
		JToolBar::getInstance('toolbar')
			->appendButton('Standard', 'edit', $alt, $task, $controller, false);
	}

	/**
	 * Writes the common 'publish' icon for the button bar.
	 *
	 * @param	string	$controller	The Controller to pass the task to.
	 * @param	string	$task		An override for the task.
	 * @param	string	$alt		An override for the alt text.
	 */
	public static function publish($controller = '', $task = 'publish', $alt = 'JTOOLBAR_PUBLISH')
	{
		JToolBar::getInstance('toolbar')
			->appendButton('Standard', 'publish', $alt, $task, $controller, false);
	}

	/**
	 * Writes the common 'unpublish' icon for the button bar.
	 *
	 * @param	string	$controller	The Controller to pass the task to.
	 * @param	string	$task		An override for the task.
	 * @param	string	$alt		An override for the alt text.
	 */
	public static function unpublish($controller = '', $task = 'unpublish', $alt = 'JTOOLBAR_UNPUBLISH')
	{
		JToolBar::getInstance('toolbar')
			->appendButton('Standard', 'unpublish', $alt, $task, $controller, false);
	}

	/**
	 * Writes the common 'archive' icon for the button bar.
	 *
	 * @param	string	$controller	The Controller to pass the task to.
	 * @param	string	$task		An override for the task.
	 * @param	string	$alt		An override for the alt text.
	 */
	public static function archive($controller = '', $task = 'archive', $alt = 'JTOOLBAR_ARCHIVE')
	{
		JToolBar::getInstance('toolbar')
			->appendButton('Standard', 'archive', $alt, $task, $controller, false);
	}

	/**
	 * Writes the common 'delete' icon for the button bar.
	 *
	 * @param	string	$controller	The Controller to pass the task to.
	 * @param	string	$task		An override for the task.
	 * @param	string	$alt		An override for the alt text.
	 */
	public static function delete($controller = '', $task = 'remove', $alt = 'JTOOLBAR_DELETE')
	{
		JToolBar::getInstance('toolbar')
			->appendButton('Standard', 'delete', $alt, $task, $controller, false);
	}

	/**
	 * Writes the common 'trash' icon for the button bar.
	 *
	 * @param	string	$controller	The Controller to pass the task to.
	 * @param	string	$task		An override for the task.
	 * @param	string	$alt		An override for the alt text.
	 */
	public static function trash($controller = '', $task = 'remove', $alt = 'JTOOLBAR_TRASH')
	{
		JToolBar::getInstance('toolbar')
			->appendButton('Standard', 'trash', $alt, $task, $controller, false);
	}

	/**
	 * Writes the common 'apply' icon for the button bar.
	 *
	 * @param	string	$controller	The Controller to pass the task to.
	 * @param	string	$task		An override for the task.
	 * @param	string	$alt		An override for the alt text.
	 */
	public static function apply($controller = '', $task = 'apply', $alt = 'JTOOLBAR_APPLY')
	{
		JToolBar::getInstance('toolbar')
			->appendButton('Standard', 'apply', $alt, $task, $controller, false);
	}

	/**
	 * Writes the common 'save' icon for the button bar.
	 *
	 * @param	string	$controller	The Controller to pass the task to.
	 * @param	string	$task		An override for the task.
	 * @param	string	$alt		An override for the alt text.
	 */
	public static function save($controller = '', $task = 'save', $alt = 'JTOOLBAR_SAVE')
	{
		JToolBar::getInstance('toolbar')
			->appendButton('Standard', 'save', $alt, $task, $controller, false);
	}

	/**
	 * Writes the common 'save2new' icon for the button bar.
	 *
	 * @param	string	$controller	The Controller to pass the task to.
	 * @param	string	$task		An override for the task.
	 * @param	string	$alt		An override for the alt text.
	 */
	public static function save2new($controller = '', $task = 'save2new', $alt = 'JTOOLBAR_SAVE_AND_NEW')
	{
		JToolBar::getInstance('toolbar')
			->appendButton('Standard', 'save-new', $alt, $task, $controller, false);
	}

	/**
	 * Writes the common 'save2copy' icon for the button bar.
	 *
	 * @param	string	$controller	The Controller to pass the task to.
	 * @param	string	$task		An override for the task.
	 * @param	string	$alt		An override for the alt text.
	 */
	public static function save2copy($controller = '', $task = 'save2copy', $alt = 'JTOOLBAR_SAVE_AS_COPY')
	{
		JToolBar::getInstance('toolbar')
			->appendButton('Standard', 'save-copy', $alt, $task, $controller, false);
	}

	/**
	 * Writes the common 'cancel' icon for the button bar.
	 *
	 * @param	string	$controller	The Controller to pass the task to.
	 * @param	string	$task		An override for the task.
	 * @param	string	$alt		An override for the alt text.
	 */
	public static function cancel($controller = '', $task = 'cancel', $alt = 'JTOOLBAR_CANCEL')
	{
		JToolBar::getInstance('toolbar')
			->appendButton('Standard', 'cancel', $alt, $task, $controller, false);
	}

	/**
	 * Writes the common 'close' icon for the button bar.
	 *
	 * @param	string	$controller	The Controller to pass the task to.
	 * @param	string	$task		An override for the task.
	 * @param	string	$alt		An override for the alt text.
	 */
	public static function close($controller = '', $task = 'cancel', $alt = 'JTOOLBAR_CLOSE')
	{
		JToolBar::getInstance('toolbar')
			->appendButton('Standard', 'cancel', $alt, $task, $controller, false);
	}
}