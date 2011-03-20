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

	protected function addToolbar()
	{
		JGImport('toolbar.helper');
		JToolBar::getInstance('toolbar')->addButtonPath(JPATH_LIBRARIES.'/games/toolbar/button');
		JHtml::_('script', 'games/toolbar.js', false, true);
		JToolBarHelper::title(JText::_($this->_option.'_'.$this->getName()), $this->getName());

		JGToolBarHelper::addNew($this->_controller_item);
		JGToolBarHelper::edit($this->_controller_item);

		JToolBarHelper::divider();
		JGToolBarHelper::publish($this->_controller_list);
		JGToolBarHelper::unpublish($this->_controller_list);

		JToolBarHelper::divider();
		JGToolBarHelper::archive($this->_controller_list);
		//if($this->state->get('filter.state') == -2){
			JGToolBarHelper::delete($this->_controller_list);
		/*}
		else {
			JGToolBarHelper::trash('games');
		}*/

		JToolBarHelper::divider();
		JToolBarHelper::preferences($this->_option);

		JToolBarHelper::divider();
		JToolBarHelper::help(''/*'JHELP_CONTENT_ARTICLE_MANAGER'*/);
	}
}