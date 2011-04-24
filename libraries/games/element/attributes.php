<?php
/**
 * @version		$Id$
 * @package		PHP Element
 * @license		GNU General Public License version 2 or later;
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Attributes class
 *
 * @package		PHP Element
 * @since		1.0
 */
class Attributes extends JObject
{
	public $styles = array();

	public function __construct($attributes = '')
	{
		if (!empty($attributes) && is_string($attributes)) $attributes = self::parseAttributes($attributes);
		return parent::__construct($attributes);
	}
	

	/**
	 * Method to extract key/value pairs out of a string with xml style attributes
	 *
	 * @param	string	$string	String containing xml style attributes
	 * @return	array	Key/Value pairs for the attributes
	 */
	public static function parseAttributes($string)
	{
		// Initialise variables.
		$retarray	= array();

		// Lets grab all the key/value pairs using a regular expression
		$found = preg_match_all('/([\w:-]+)[\s]?=[\s]?"([^"]*)"/i', $string, $matches, PREG_SET_ORDER);

		if ($found)
		{
			foreach ($matches as $matche)
			{
				if ($matche[1] == $matche[2]) $matche[2] = true;
				$retarray[$matche[1]] = $matche[2];
			}
		}

		return $retarray;
	}

	public function __toString()
	{
		$string = ' ';
		$attributes = $this->getProperties();
		foreach($attributes as $attribute => $value)
		{
			if (empty($value)) continue;
			if ($attribute == 'styles')
			{
				$tmp = '';
				foreach ($value as $k => $v)
				{
					$tmp .= $k.':'.$v.';';
				}
				$value = $tmp;
			}
			if ($value === true) {
				$value = $attribute;
			}
			$string .= $attribute.'="'.$value.'" ';
		}
		return rtrim($string);
	}
}