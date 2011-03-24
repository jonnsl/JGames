<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Plugins
 * @license		GNU General Public License version 3
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * JGames Content integration Plugin
 *
 * @package		JGames
 * @subpackage	Content Integration
 */
class plgContentGames extends JPlugin
{
	public function __construct(&$subject, $config = array())
	{
		$this->enabled = JComponentHelper::isEnabled('com_games');
		return parent::__construct($subject, $config);
	}

	public function onContentPrepareForm($form, $data)
	{
		if ($form->getName() != 'com_content.article' || !$this->enabled) return;

		// Load the custom form
		$this->loadLanguage();
		$form->loadFile(dirname(__FILE__).'/forms/form.xml');

		// Load the custom data
		if(isset($data->id) && $data->id){
			$form->bind($this->getData($data->id));
		}
		return true;
	}

	public function onContentBeforeSave($context, $table, $isNew)
	{
		if ( !($context == 'com_content.article'
			|| $context == 'com_content.form')
			|| !$this->enabled) return;

		$attribs = new JRegistry($table->attribs);
		$attribs = $attribs->toArray();

		if (isset($attribs['game'])) {
			$this->gameId = (int)$attribs['game']['id'];
			unset($attribs['game']);
		}

		$attribs = new JRegistry($attribs);
		$table->attribs = (string)$attribs;
		return true;
	}

	public function onContentAfterSave($context, $table, $isNew)
	{
		if ( !($context == 'com_content.article'
			|| $context == 'com_content.form')
			|| !$this->enabled) return;

		$db = JFactory::getDbo();

		$db->setQuery('DELETE FROM `#__games_content` WHERE content_id = '.(int)$table->id);
		$db->query();
		$db->setQuery('INSERT INTO `#__games_content` (`content_id`, `game_id`)' .
				' VALUES ('.$table->id.', '.$this->gameId.')');
		$db->query();

		return true;
	}
	
	public function onContentGetQuery($query)
	{
		if (JRequest::getCmd('option') == 'com_games' && JRequest::getCmd('view') == 'game')
		{
			$query->leftjoin('#__games_content as gc ON gc.content_id = a.id')
				->where('gc.game_id = '.JRequest::getInt('id'));
		}
	}

	public function onContentGetStoreId($id)
	{
		if (JRequest::getCmd('option') == 'com_games' && JRequest::getCmd('view') == 'game')
		{
			$id = ':'.JRequest::getInt('id');
		}
	}

	private function getData($id)
	{
		$result = new JObject;
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('gc.game_id as id, g.title as name')
			->from('#__games_content as gc')
			->leftJoin('#__games as g ON g.id = gc.game_id')
			->where('content_id = '.(int)$id);
		$db->setQuery($query);
		$game_id = $db->loadAssoc();
		if (!empty($game_id)) $result->attribs['game'] = $game_id;

		return $result;
	}
}