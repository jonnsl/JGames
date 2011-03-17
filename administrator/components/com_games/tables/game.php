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
 * Table class
 */
class JTableGame extends GamesTable
{
	public $genres;
	public $platforms;
	public function __construct($db)
	{
		parent::__construct('#__games', 'id', $db);
	}

	public function check()
	{
		if(empty($this->alias)) $this->alias = $this->title;
		$this->alias = JApplication::stringURLSafe($this->alias);
		$this->genres = GamesArrayHelper::excludeEmptyValues($this->genres);
		$this->platforms = GamesArrayHelper::excludeEmptyValues($this->platforms);

		return true;
	}

	public function bind($src, $ignore = '')
	{
		if(isset($src['pegi_content']) && is_array($src['pegi_content']))
		{
			$registry				= new JRegistry($src['pegi_content']);
			$src['pegi_content']	= (string) $registry;
		}

		if(isset($src['esrb_content']) && is_array($src['esrb_content']))
		{
			$registry				= new JRegistry($src['esrb_content']);
			$src['esrb_content']	= (string) $registry;
		}

		if(isset($src['boxarts']) && is_array($src['boxarts']))
		{
			$registry		= new JRegistry($src['boxarts']);
			$src['boxarts']	= (string) $registry;
		}

		return parent::bind($src, $ignore);

	}
	/**
	 * Method to load a row from the database by primary key and bind the fields
	 * to the JTable instance properties.
	 *
	 * @param	mixed	An optional primary key value to load the row by, or an array of fields to match.  If not
	 *					set the instance property value is used.
	 * @param	boolean	True to reset the default values before loading the new row.
	 * @return	boolean	True if successful. False if row not found or on error (internal error state set in that case).
	 */
	public function load($keys = null, $reset = true)
	{
		if(!parent::load($keys, $reset)) return false;

		// load the genres
		if(!$this->loadGenres()) return false;

		// load the platforms
		if(!$this->loadPlatforms()) return false;

		return true;
	}

	public function loadGenres()
	{
		$query = $this->_db->getQuery(true);
		$query->select('m.genre_id');
		$query->from('#__games AS a');
		$query->leftjoin('#__games_genre_map as m ON m.game_id = a.id');
		$query->where('a.id = '.(int) $this->id);
		$this->_db->setQuery($query);

		$genres = $this->_db->loadResultArray();

		if($this->_db->getErrorNum()) {
			$e = new JException('could not load genres for game:"'.$this->id.'"');
			$this->setError($e);
			return false;
		}

		$this->genres = empty($genres[0])? array (0): $genres;

		return true;
	}

	public function loadPlatforms()
	{
		$query = $this->_db->getQuery(true);
		$query->select('m.platform_id');
		$query->from('#__games AS a');
		$query->leftjoin('#__games_platform_map as m ON m.game_id = a.id');
		$query->where('a.id = '.(int) $this->id);
		$this->_db->setQuery($query);

		$platforms = $this->_db->loadResultArray();

		if($this->_db->getErrorNum()) {
			$e = new JException('could not load platforms for game:"'.$this->id.'"');
			$this->setError($e);
			return false;
		}

		$this->platforms = empty($platforms[0])? array (0): $platforms;

		return true;
	}

	public function store($updateNulls = false)
	{
		$genres		= (array) $this->genres;
		unset($this->genres);
		$platforms	= (array) $this->platforms;
		unset($this->platforms);

		if(!parent::store($updateNulls)) return false;

		// store the genres
		if(!$this->storeGenres($genres)) return false;

		// store the platforms
		if(!$this->storePlatforms($platforms)) return false;

		return true;
	}

	public function storeGenres($genres)
	{
		$query = $this->_db->getQuery(true);
		$query->delete();
		$query->from('#__games_genre_map');
		$query->where('game_id = '.(int) $this->id);
		$this->_db->setQuery($query);

		// Check for a database error.
		if(!$this->_db->query()) {
			$e = new JException('could not save genres for game:"'.$this->id.'"'.$this->_db->getErrorMsg());
			$this->setError($e);
			return false;
		}

		if(!empty($genres)) {
			// Set the new game genres maps.
			$this->_db->setQuery(
				'INSERT INTO `#__games_genre_map` (`game_id`, `genre_id`)' .
				' VALUES ('.$this->id.', '.implode('), ('.$this->id.', ', $genres).')'
			);

			// Check for a database error.
			if (!$this->_db->query()) {
				$e = new JException('could not save genres for game:"'.$this->id.'"'.$this->_db->getErrorMsg());
				$this->setError($e);
				return false;
			}
		}

		return true;
	}

	public function storePlatforms($platforms)
	{
		$query = $this->_db->getQuery(true);
		$query->delete();
		$query->from('#__games_platform_map');
		$query->where('game_id = '.(int) $this->id);
		$this->_db->setQuery($query);

		// Check for a database error.
		if(!$this->_db->query()) {
			$e = new JException('could not save platforms for game:"'.$this->id.'"'.$this->_db->getErrorMsg());
			$this->setError($e);
			return false;
		}

		if(!empty($platforms)) {
			// Set the new game platforms maps.
			$this->_db->setQuery(
				'INSERT INTO `#__games_platform_map` (`game_id`, `platform_id`)' .
				' VALUES ('.$this->id.', '.implode('), ('.$this->id.', ', $platforms).')'
			);

			// Check for a database error.
			if (!$this->_db->query()) {
				$e = new JException('could not save platforms for game:"'.$this->id.'"'.$this->_db->getErrorMsg());
				$this->setError($e);
				return false;
			}
		}

		return true;
	}
}