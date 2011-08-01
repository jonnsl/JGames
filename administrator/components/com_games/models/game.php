<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Administrator
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.form.form');
JGImport('application.component.modelForm');

/**
 *
 */
class GamesModelGame extends JGModelForm
{
	public function getPlatformsParams()
	{
		$db = $this->db;
		$query = $db->getQuery(true)
			->select('c.id, c.params')
			->from('#__categories as c')
			->where('c.extension = "com_games.platforms"');
		$db->setQuery($query);
		$result = $db->loadObjectList();
		$params = array();

		foreach ($result as $v) {
			$params[$v->id] = json_decode($v->params);
		}
		return json_encode($params);
	}

	/**
	 * Method to save the form data.
	 * TODO: Create a generic function in GamesModelForm
	 * @param	array	$data	The form data.
	 * @return	boolean	True on success.
	 */
	public function save(&$data)
	{
		// Initialise variables;
		$dispatcher = JDispatcher::getInstance();
		$table		= $this->getTable();
		$pk			= (!empty($data['id'])) ? $data['id'] : 0;
		$isNew		= true;

		// Include the content plugins for the on save events.
		JPluginHelper::importPlugin('games');

		// Load the row if saving an existing record.
		if ($pk > 0) {
			$table->load($pk);
			$isNew = false;
		}

		// Deal with boxarts
		$this->boxart($data['boxarts'], $data['title']);

		// Bind the Achievements
		try
		{
			require_once JPATH_LIBRARIES.'/games/steam-condenser/steam-condenser.php';
			$id = new SteamId('ChetFaliszek');
			$stats = $id->getGameStats($data['title']);
			$achievements = serialize($stats->getAchievements());
		}
		catch(Exception $e)
		{
			JError::raiseNotice(0, 'Could not get Achiements from STEAM: '.$e->getMessage());
			$data['achievements'] = '';
		}

		// Bind the data.
		if (!$table->bind($data)) {
			$this->setError($table->getError());
			return false;
		}
		

		// Check the data.
		if (!$table->check()) {
			$this->setError($table->getError());
			return false;
		}

		// Trigger the onContentBeforeSave event.
		$result = $dispatcher->trigger('onGameBeforeSave', array($this->option.'.'.$this->name, $table, $isNew));
		if (in_array(false, $result, true)) {
			$this->setError($table->getError());
			return false;
		}

		// Store the data.
		if (!$table->store()) {
			$this->setError($table->getError());
			return false;
		}

		// Clean the cache.
		$cache = JFactory::getCache($this->option);
		$cache->clean();

		// Trigger the onContentAfterSave event.
		$dispatcher->trigger('onGameAfterSave', array($this->option.'.'.$this->name, $table, $isNew));

		return true;
	}

	public function getItem($pk = null)
	{
		if (($item = parent::getItem($pk)) == false) {
			return false;
		}

		if(isset($item->pegi_content))
		{
			$registry = new JRegistry($item->pegi_content);
			$item->pegi_content = $registry->toArray();
		}

		if(isset($item->esrb_content))
		{
			$registry = new JRegistry($item->esrb_content);
			$item->esrb_content = $registry->toArray();
		}

		if(empty($item->boxarts)) $item->boxarts = '{}';

		return $item;
	}



	protected function boxart(&$data, $title)
	{
		if(empty($data))return true;
		// Verifica se as plataformas existem e pega os parametros
		$platformsIds = array_keys($data);
		//$platformsIds = JArrayHelper::toInteger($platformsIds);
		$query = $this->db->getQuery(true);
		$query->select('c.params, c.alias, c.id, c.title');
		$query->from('#__categories as c');
		$query->where('c.extension = "com_games.platforms" AND (c.id = '.implode(' OR c.id = ', $platformsIds).')');
		$this->db->setQuery($query);
		$platforms = $this->db->loadAssocList('id');

		if($this->db->getErrorNum()) {
			JError::raiseWarning($this->db->getErrorNum(), 'Could not get the plataform data to save the boxart: '.$this->db->getErrorMsg());
			return false;
		}

		foreach ($data as $platformId => $file)
		{
			$relative_path = str_replace('/', DS, $file);
			$full_path = JPATH_ROOT.DS.$relative_path;
			$file_name = JFile::getName($relative_path);

			if(isset($platforms[$platformId]))
			{
				$platform = $platforms[$platformId];
				$platform['params'] = new JRegistry($platform['params']);
				if(!JFile::exists($full_path)) {
					JError::raiseNotice(0, 'The given Boxart for "'.$platform['title'].'" do not exist.');
					continue;
				}

				// Verificar o local do arquivo (deve ser images/games/boxarts/$aliasdaplataforma)
				if (!JFolder::exists(JPATH_ROOT.DS.'images'.DS.'games'.DS.'boxarts'.DS.$platform['alias'])) JFolder::create(JPATH_ROOT.DS.'images'.DS.'games'.DS.'boxarts'.DS.$platform['alias']);
				$tmp = explode(DS, $relative_path);
				array_pop($tmp);
				$tmp = implode(DS, $tmp);
				if($tmp != 'images'.DS.'games'.DS.'boxarts'.DS.$platform['alias'])
				{
					$relative_path = 'images'.DS.'games'.DS.'boxarts'.DS.$platform['alias'].DS.$file_name;
					$dest = JPATH_ROOT.DS.$relative_path;
					if(!JFile::move($full_path, $dest)) {
						JError::raiseNotice(0, 'Could not move the boxart ('.$full_path.') to the correct place ('.$dest.')');
						continue;
					}
					$full_path = $dest;
				}

				$title = JApplication::stringURLSafe($title);
				$title = JFile::makeSafe($title);
				// Verificar o nome do arquivo (deve ser o alias do jogo)
				if (JFile::stripExt($file_name) != $title)
				{
					$dest = preg_replace('@[^\\\]*(\.png|\.gif|\.jpg|\.bmp|\.jpeg|\.psd|\.eps)$@i', $title.'$1', $full_path);
					//$dest = str_replace(JFile::stripExt($file_name), $title, $full_path);
					if(!JFile::move($full_path, $dest)) {
						JError::raiseNotice(0, 'Could not rename the boxart ('.$file_name.') to the correct name ('.$title.'.'.JFile::getExt($file_name).')');
						continue;
					}
					$full_path = $dest;
					$relative_path = preg_replace('@[^\\\]*(\.png|\.gif|\.jpg|\.bmp|\.jpeg|\.psd|\.eps)$@i', $title.'$1', $relative_path);
				}

				if(!$this->createBoxartThumb($full_path, (int)$platform['params']->get('width', 120), (int)$platform['params']->get('height', 165))){
					JError::raiseNotice(0, 'Could not create the boxart thumbnail for the platform:"'.$platform['title'].'"');
				}

				$data[$platformId]= str_replace(DS, '/', $relative_path);
			}
			else {
				unset($data[$platformId]);
				JError::raiseNotice(0, 'Platform #'.$platformId.' do not exist or is not selected.');
			}
		}

		return true;
	}

	protected function createBoxartThumb($file, $thumb_width, $thumb_height)
	{
		$new_file = preg_replace('/(\.jpg)$/i', '_thumb$1', $file);

		// Don't make unecessary work if the thumbnail already exists and has the right size
		if(JFile::exists($new_file)){
			list($old_file_width,$old_file_height) = getimagesize($new_file);
			if($old_file_width== $thumb_width && $old_file_height==  $thumb_height)return true;
		}

		if(!$size = getimagesize($file)) return false;
		list($width, $height) = $size;

		if(
			!($image_p = imagecreatetruecolor($thumb_width, $thumb_height)) ||
			!($image = imagecreatefromjpeg($file)) ||
			!imagecopyresampled($image_p, $image, 0, 0, 0, 0, $thumb_width, $thumb_height, $width, $height) ||
			!imagejpeg($image_p, $new_file, 90) ||
			!imagedestroy($image_p)
		){
			return false;
		}

		return true;
	}
}