<?php
/**
 * @version		$Id$
 * @package		com_games
 * @subpackage	Libraries
 * @license		GNU General Public License version 3
 */

// No direct access
defined('_JEXEC') or die;

class GamesXhtml extends JObject
{
	/**
	 *
	 *
	 * @var array
	 */
	protected $inlineClose = array('area', 'base', 'basefront', 'br', 'col', 'hr', 'img', 'link', 'meta', 'param', 'input');

	protected $attributes = array();
	/**
	 *
	 *
	 * @param	$type		The type of the xhtml element
	 * @param	$properties	The attributes  for the xhtml element
	 * @return	object	JXhtml
	 */
	public function __construct($type, $properties = array())
	{
		$document		= JFactory::getDocument();
		$this->_lnEnd	= $document->_getLineEnd();
		$this->_tab		= $document->_getTab();
		$this->_inner	= '';
		$this->setType($type);
		return parent::__construct($properties);
	}

	/**
	 *
	 *
	 * @param
	 * @param
	 * @return
	 */
	public function set($property, $value)
	{
		$previous = isset($this->attributes [$property]) ? $this->attributes [$property] : null;
		$this->attributes [$property] = $value;
		return $previous;
	}

	/**
	 *
	 *
	 * @param
	 * @param
	 * @return
	 */
	public function get($property, $default = null)
	{
		if (isset($this->attributes [$property])) {
			return $this->attributes [$property];
		}
		return $default;
	}

	/**
	 *
	 *
	 * @param
	 * @return
	 */
	public function setType($type)
	{
		$this-> _type = strtolower(trim($type));

		if(in_array($type, $this->inlineClose))
		{
			$this->inlineClose = true;
		}
		else
		{
			$this->inlineClose = false;
		}
	}

	/**
	 *
	 *
	 * @param
	 * @return
	 */
	public function setText($text)
	{
		$this->_inner = trim(htmlspecialchars((string)$text, ENT_COMPAT, 'UTF-8'));
		return $this;
	}

	/**
	 *
	 *
	 * @param
	 * @return
	 */
	public function addText($text)
	{
		$this->_inner .= trim(htmlspecialchars((string)$text, ENT_COMPAT, 'UTF-8'));
		return $this;
	}

	/**
	 *
	 *
	 * @param
	 * @return
	 */
	public function setHtml($html)
	{
		$this->_inner = $this->_tab.(string) $html;
		return $this;
	}

	/**
	 *
	 *
	 * @param
	 * @return
	 */
	public function addHtml($html)
	{
		$this->_inner .= (string) $html;
		return $this;
	}

	/**
	 * Check if a class exists.
	 *
	 * @param	string	$class	The class to check.
	 * @return	boolean	True if the class exists, false otherwise.
	 */
	public function hasClass($class)
	{
		return (preg_match('#(^| )'.$class.'( |$)#', $this->class));
	}

	/**
	 * Add a class only if doesn't already exists.
	 *
	 * @param	string	$class	The class to add.
	 * @return	object	This object.
	 */
	public function addClass($class)
	{
		if(!$this->hasClass($class))
		{
			if($this->class) {
				$this->set('class', $this->get('class').' '.$class);
			}
			else {
				$this->set('class', $class);
			}
		}
		return $this;
	}

	/**
	 * Remove a class only if it exists.
	 *
	 * @param	string	$class	The class to remove.
	 * @return	object	This object.
	 */
	public function removeClass($class)
	{
		if($this->hasClass($class))
		{
			$this->set('class', preg_replace('#(^| )'.$class.'( |$)#', '', $this->get('class')));
			$this->set('class', preg_replace('# $#', '', $this->get('class')));
		}
		return $this;
	}

	/**
	 *
	 *
	 * @param
	 * @param
	 * @return
	 */
	public function addEvent($name, $function)
	{
		return $this->set('on'.$name, $function);
	}

	/**
	 *
	 *
	 * @return
	 */
	protected function render()
	{
		if(!isset($this->rendered))
		{
			$element = '<'.$this->_type.' ';
			foreach ($this->attributes  as $name => $value) {
				if (!empty($value)) {
					if($name == 'readonly' && ($value == 'true' || $value == 'readonly') ||
					$name == 'disabled' && ($value == 'true' || $value == 'disabled') ||
					$name == 'checked' && ($value == 'true' || $value == 'checked') ) {
						$element .= $name.'="'.$name.'" ';
					} else {
						$element .= $name.'="'.$value.'" ';
					}
				}
			}
			if($this->inlineClose) {
				$element .= '/>'.$this->_lnEnd;
			} else {
				$element .= '>'.(string)$this->_inner.'</'.$this->_type.'>';
			}
		}

		return $this->rendered = $element;
	}

	/**
	 *
	 *
	 * @return
	 */
	public function __toString()
	{
		return $this->render();
	}
}