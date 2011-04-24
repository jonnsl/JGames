<?php
/**
 * @version		$Id$
 * @package		PHP Element
 * @license		GNU General Public License version 2 or later;
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Elements class
 *
 * @package		PHP Element
 * @since		1.0
 */
class TextNode
{
	public function __construct($text)
	{
		$this->_data = htmlspecialchars(trim((string)$text), ENT_COMPAT, 'UTF-8');
	}
	
	public function __toString()
	{
		return $this->_data;
	}
}