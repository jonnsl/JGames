<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Libraries
 * @license		GNU General Public License version 3
 */

// No direct access
defined('_JEXEC') or die;

JGImport('application.component.view');
JGImport('utilities.inflect');

/**
 *
 */
class JGViewList extends JGView
{
	/**
	 * Array of data items.
	 * 
	 * @var array
	 */
	protected $items;

	/**
	 * A JPagination object for the data set.
	 * 
	 * @var object JPagination
	 */
	protected $pagination;

	/**
	 * An state object
	 *
	 * @var	JObject
	 */
	protected $state;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Guess the list controller as the name.
		if (empty($this->_controller_list)) {
			$this->_controller_list = (isset($config['controller_list'])) ? $config['controller_list'] : $this->getName();
		}

		// Guess the item controller as the singular of the list controller.
		if (empty($this->_controller_item)) {
			$this->_controller_item = (isset($config['controller_item'])) ? $config['controller_item'] : JGInflect::singularize($this->_controller_list);
		}
	}
	
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		parent::display($tpl);
	}
}