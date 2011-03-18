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
		parent::__construct($config);
		// FIXME GamesController should not create multiple instances of the same model
		$this->model =  $this->getModel();
	}

	public function display()
	{
		// FIXME there should be a way to unregister this function
		return JError::raiseError(404, JText::sprintf('JLIB_APPLICATION_ERROR_TASK_NOT_FOUND', 'Display'));
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

	// translate from words to numbers
	// add error handling
	public function autoSearchESRB()
	{
		$title = str_replace(' ', '+', JRequest::getString('title'));
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://www.esrb.org/ratings/search.jsp?titleOrPublisher='.$title.'&ratingsCriteria=&platforms=&platformsCriteria=&searchVersion=compact&content=&searchType=title&contentCriteria=&newSearch.x=45&newSearch.y=14');
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
		curl_setopt($ch, CURLOPT_REFERER, 'http://www.esrb.org/ratings/search.jsp');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.10 (KHTML, like Gecko) Chrome/8.0.552.215 Safari/534.10');

		$file_contents = curl_exec($ch);
		if($file_contents === false) {
			JError::raiseError(curl_errno($ch), curl_error($ch));
			curl_close($ch);
		}
		curl_close($ch);

		$matches = array();
		preg_match_all('/<td( class="[a-z]*")?>[^<]*/i', $file_contents, $matches);

		/*$matches = htmlspecialchars(print_r($matches, 1));
		echo '<pre>'.$matches.'</pre>';
		exit();*/
		$result = array(
			'rating' => str_replace('<td class="center">', '', $matches[0][9]),
			'content' => str_replace('<td>', '', $matches[0][10])
		);
		echo json_encode($result);
	}
}