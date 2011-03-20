<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Administrator
 * @license		GNU General Public License version 3
 */

// No direct access
defined('_JEXEC') or die;

/**
 *
 */
class GamesControllerAjax extends JGController
{
	public function __construct($config = array())
	{
		if (!isset($config['default_task'])) {
			$config['default_task'] = 'autoComplete';
		}
		parent::__construct($config);
		$this->unregisterTask('display');
		// FIXME GamesController should not create multiple instances of the same model
		$this->model =  $this->getModel();
	}

	public function autoComplete()
	{
		$type = JRequest::getCmd('w');
		$allowedTypes = array('developer', 'serie', 'publisher');
		$type = in_array($type, $allowedTypes)? $type : 'Developer';
		$this->model->setState('filter.type', $type);
		$items = $this->model->getItems();
		if($items === false) {
			$items = array('erro' => $this->model->getError());
		}

		echo json_encode($items);
	}
}