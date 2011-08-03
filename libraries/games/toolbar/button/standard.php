<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Libraries
 * @license		GNU General Public License version 3
 */

// No direct access
defined('_JEXEC') or die;

/**
 * 
 */
class JButtonStandard extends JObject
{
	/**
	 * Button type
	 *
	 * @var string
	 */
	protected $name = 'Standard';
	
	/**
	 * The JToolbar object that instantiated the element
	 *
	 * @var object
	 */
	protected $toolBar = null;

	/**
	 * Constructor
	 */
	public function __construct($toolBar)
	{
		$this->toolBar = $toolBar;
	}

	/**
	 * Get the button name
	 *
	 * @return string The button name
	 */
	public function getName()
	{
		return $this->name;
	}

	public function render($definition)
	{
		$html	= '';
		$id		= call_user_func_array(array($this, 'fetchId'), $definition);
		$action	= call_user_func_array(array($this, 'fetchButton'), $definition);

		// Build id attribute
		if ($id) {
			$id = ' id="'.$id.'"';
		}

		// Build the HTML Button
		$html	.= '<li class="button"'.$id.'>';
		$html	.= $action;
		$html	.= '</li>';

		return $html;
	}

	/**
	 * Method to get the CSS class name for an icon identifier
	 *
	 * Can be redefined in the final class
	 *
	 * @param	string	$identifier	Icon identification string
	 * @return	string	CSS class name
	 */
	public function fetchIconClass($identifier)
	{
		return 'icon-32-'.$identifier;
	}

	/**
	 * Get the button
	 */
	public function fetchButton($type='Standard', $name = '', $text = '', $task = '', $controller = '', $list = true)
	{
		$class	= $this->fetchIconClass($name);

		$html	 = '<a href="#" class="toolbar" data-task="'.$task.'" data-controller="'.$controller.'">';
		$html	.= '<span class="'.$class.'"></span>';
		$html	.= JText::_($text);
		$html	.= '</a>';

		return $html;
	}

	/**
	 * Get the button CSS Id
	 *
	 * @return	string	Button CSS Id
	 */
	public function fetchId($type = 'Standard', $name = '')
	{
		return $this->toolBar->getName().'-'.$name;
	}
}