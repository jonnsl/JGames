<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Libraries
 * @license		GNU General Public License version 3
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.utilities.arrayHelper');

/**
 *
 */
class JGArrayHelper extends JArrayHelper
{
	/**
	 * Utility function to map an array to a object.
	 *
	 * @param	array	$array		The array to map.
	 * @param	string	$class		Name of the class to create
	 * @param	boolean	$recursive
	 * @return	object
	 */
	static function toObject($array, $class = 'JObject', $recursive = false)
	{
		$obj = new $class();
		if (is_array($array)) {
			foreach ($array as $k => $v) {
				if ($recursive && is_array($v)) {
					$obj->$k = self::toObject($v, $class, true);
				} else {
					$obj->$k = $v;
				}
			}
		}
		return $obj;
	}

	public static function resetKeys($array)
	{
		$newArray = array();
		foreach($array as $v) $newArray[] = $v;
		return $newArray;
	}

	/**
	 * Utility function to exclude empty values from an array.
	 *
	 * @param	array	$array		The array to map.
	 * @param	boolean	$recursive
	 * @return	object
	 */
	static function excludeEmptyValues($array, $recursive = false)
	{
		if (is_array($array)) {
			foreach ($array as $k => $v) {
				if(empty($v)) {
					unset($array[$k]);
					continue;
				}
				if ($recursive && is_array($v)) {
					$array[$k] = self::excludeEmptyValues($v, true);
				}
			}
		}
		return $array;
	}
}