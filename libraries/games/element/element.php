<?php
/**
 * @version		$Id$
 * @package		PHP Element
 * @license		GNU General Public License version 2 or later;
 */

// No direct access
defined('_JEXEC') or die;

require dirname(__FILE__).'/attributes.php';
require dirname(__FILE__).'/elements.php';
require dirname(__FILE__).'/textnode.php';

/**
 * Element class
 *
 * @package		PHP Element
 * @since		1.0
 */
class Element extends JObject
{
	/**
	 * An array of elements that are closed inline,
	 * or true if the tag of the element was set.
	 *
	 * @var mixed 
	 */
	protected $inlineClose = array('area', 'base', 'basefront', 'br', 'col', 'hr', 'img', 'link', 'meta', 'param', 'input');

	/**
	 * Any valid xhtml tag.
	 * 
	 * @var string
	 */
	protected $tag;

	/**
	 * Object holding the Attributes of this Element
	 * 
	 * @var	object	Attributes
	 */
	protected $attributes;

	/**
	 * Holds the parent element
	 *
	 * @var Element Object
	 */
	public $parent;

	/**
	 * Object containing all the immediate children
	 *
	 * @var Object Elements
	 */
	protected $children;

	/**
	 * Object containing all the Sibling that are before this element.
	 *
	 * @var Object Elements
	 */
	protected $lftSibling;

	/**
	 * Object containing all the Sibling that are after this element.
	 *
	 * @var Object Elements
	 */
	protected $rgtSibling;

	/**
	 *
	 *
	 * @param	$type		The type of the xhtml element
	 * @param	$properties	The attributes  for the xhtml element
	 * @return	object	JXhtml
	 */
	public function __construct($type, $properties = array())
	{
		if (strpos($type, '#')) {
			list($type, $id) = explode('#', $type);
			if (!isset($properties['id'])) $properties['id'] = $id;
		}
		$this->set('tag', $type);
		$this->attributes	= new Attributes;
		$this->children		= new Elements;
		$this->lftSibling	= new Elements;
		$this->rgtSibling	= new Elements;
		return parent::__construct($properties);
	}

	/**
	 *
	 *
	 * @param
	 * @param
	 * @return
	 */
	public function set($property, $value = null)
	{
		// One argument version
		if (is_array($property))
		{
			foreach ($property as $prop => $value)
			{
				if (!is_string($prop)) continue;
				$this->set($prop, $value);
			}
			return $this;
		}

		// Using Custom Setters
		$method_name = 'set'.ucfirst(strtolower($property));
		if (method_exists($this, $method_name)) return $this->$method_name($value);

		// Fallback to Element Attributes:
		$this->attributes->$property = $value;

		return $this;
	}

	/**
	 * This is a "dynamic arguments" method. Properties passed in can be any of the 'get' properties in the Element.Properties Object.
	 *
	 * @param	string	$property	The string key from the Element::$attributes array representing the property to get.
	 * @param	mixed	$default	The default value if the attribute doesn't exists.
	 * @return	mixed	The result of calling the corresponding 'get' function in the Element.Properties Object.
	 */
	public function get($property, $default = null)
	{
		// Using Custom Getters
		$method_name = 'get'.ucfirst(strtolower($property));
		if (method_exists($this, $method_name)) return $this->$method_name();

		// Fallback to Element Attributes:
		return $this->getProperty($property, $default);
	}

	public function erase($property)
	{
		// Using Custom Erase
		$method_name = 'erase'.ucfirst(strtolower($property));
		if (method_exists($this, $method_name)) return $this->$method_name();

		// Fallback to Element Attributes:
		return $this->removeProperty($property);
	}

	/**
	 * Sets an attribute or special property for this Element.
	 *
	 * @param	string	$property	The property to assign the value passed in.
	 * @param	mixed	$default	The value to assign to the property passed in.
	 * @return	object	This Element.
	 */
	public function setProperty($property, $value)
	{
		$this->attributes->$property = $value;
		return $default;
	}

	/**
	 * Sets numerous attributes for the Element.
	 *
	 * @param	string	An associative array of properties => values
	 * @return	object	This Element.
	 */
	public function setProperties($properties)
	{
		foreach($properties as $property => $value)
		{
			$this->attributes->$property = $value;
		}

		return $this;
	}

	/**
	 * Returns a single element attribute.
	 *
	 * @param	string	$property	The property to be retrieved.
	 * @param	mixed	$default	The default value if the attribute doesn't exists.
	 * @return	mixed	A string containing the Element's requested property.
	 */
	public function getProperty($property, $default = null)
	{
		if (isset($this->attributes->$property)) {
			return $this->attributes->$property;
		}

		return $default;
	}

	/**
	 * Returns a single element attribute.
	 *
	 * @param	string	Any number of properties to be retrieved.
	 * @return	array	An associative array containing all of the Element's requested properties.
	 */
	public function getProperties()
	{
		$array = array();
		$properties = func_get_args();
		foreach($properties as $property)
		{
			$array[(string)$property] = $this->getProperty($property);
		}

		return $array;
	}

	/**
	 * Removes an attribute from the Element.
	 *
	 * @param	string	The attribute to remove.
	 * @return	object	This Element.
	 */
	public function removeProperty($property)
	{
		if (isset($this->attributes->$property))
		{
			unset($this->attributes->$property);
		}

		return $this;
	}
	
	/**
	 * Removes an attribute from the Element.
	 *
	 * @param	string	The attribute to remove.
	 * @return	object	This Element.
	 */
	public function removeProperties()
	{
		$properties = func_get_args();
		foreach($properties as $property) {
			$this->removeProperty($property);
		}
		return $this;
	}

	/**
	 * Tests the Element to see if it has the passed in className.
	 *
	 * @param	string	$class	The class name to test.
	 * @return	boolean	True if the Element has the class, otherwise false.
	 */
	public function hasClass($class)
	{
		return (bool)(preg_match('#(^|\s*)'.$class.'(\s*|$)#', $this->get('class')));
	}

	/**
	 * Adds the passed in class to the Element, if the Element doesnt already have it.
	 *
	 * @param	string	$class	The class name to add.
	 * @return	object	This object.
	 */
	public function addClass($class)
	{
		if(!$this->hasClass($class))
		{
			if($this->get('class')) {
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
	 * Adds or removes the passed in class name to the Element, depending on whether or not it's already present.
	 * 
	 * @param	string	$class	The class to add or remove.
	 * @param	bool	$force	Force the class to be either added or removed
	 * @return	object	This object.
	 */
	public function toggleClass($class, $force = null)
	{
		if ($force == null) $force = !$this->hasClass($class);
		return ($force) ? $this->addClass($class) : $this->removeClass($class);
	}

	/**
	 * Works like Element::grab, but allows multiple elements to be adopted and only appended at the bottom.
	 * Inserts the passed element(s) inside the Element (which will then become the parent element).
	 * 
	 * @param	mixed	$el		An Element object, or an array of elements.
	 * @param	object	$others	One or more additional Elements separated by a comma or as an array.
	 * @return	object	This Element
	 */
	public function adopt($el, $others = null)
	{
		$args = func_get_args();

		$el = array_shift($args);
		if (!is_array($el)) $el = (array) $el;

		$elements = $el;		

		if (!empty($args))
		{
			foreach ($args as $arg)
			{
				$elements = array_merge($elements, (array) $arg);
			}
		}

		foreach ($elements as $element)
		{
			if ($element instanceof Element) $this->grab($element, 'bottom');
		}

		return $this;
	}

	/**
	 * Works like Element:grab, but instead of accepting an id or an element,
	 * it only accepts text. A text node will be created inside this Element,
	 * in either the top or bottom position.
	 *
	 * @param	string	$text	The text to append.
	 * @param	string	$where	The position to inject the text to. Values accepted are 'top', 'bottom', 'before' and 'after'.
	 * @return Object The current Element instance
	 */
	public function appendText($text, $where = 'bottom')
	{
		return $this->grab(new TextNode($text), $where);
	}

	/**
	 * Works as Element::inject, but in reverse.
	 * 
	 * @param	mixed	$el		An Element object.
	 * @param	string	$where	The place to insert the passed in element. Can be 'top' or 'bottom'.
	 * @return	object	This Element
	 */
	public function grab($el, $where = 'bottom')
	{
		$el->parent = $this;
		$this->children->add($el, $where);
		return $this;
	}

	/**
	 * Injects, or inserts, the Element at a particular place relative to the Element's children (specified by the second the argument).
	 * 
	 * @param	object	$el		An Element object.
	 * @param	string	$where	The place to inject this Element. Can be 'top', 'bottom', 'after', or 'before'.
	 * @return	object	This Element
	 */
	public function inject(Element $el, $where = 'bottom')
	{
		$el->grab($this, $where);
		return $this;
	}

	/**
	 * Works like Element::grab, but replaces the element in its place, and then appends the replaced element in the location specified inside the this element.
	 * 
	 * @param	mixed	$el		An Element object.
	 * @param	string	$where	The place to insert the passed in element. Can be 'top' or 'bottom'.
	 * @return	object	This Element
	 */
	public function wraps($el, $where = 'bottom')
	{
		
	}

	/**
	 * Returns the previousSibling of the Element (excluding text nodes).
	 * 
	 * @param	string	$match	A tag name to match the the found element(s) with.
	 * @return	mixed	The previous sibling Element or null if none found.
	 */
	public function getPrevious($match = '')
	{
		$reverse = array_reverse($this->lftSibling);
		
		foreach ($reverse as $el)
		{
			if (!$match) {
				return $el;
			}
			elseif($el->getTag() == $match) {
				return $el;
			}
		}
		
		return null;
	}

	/**
	 * Like Element:getPrevious, but returns a collection of all the matched previousSiblings.
	 */
	public function getAllPrevious()
	{
		return $this->lftSibling;
	}

	/**
	 * As Element::getPrevious, but tries to find the nextSibling (excluding text nodes).
	 * 
	 * @param	string	$match	A comma seperated list of tag names to match the found element(s) with.
	 * @return	mixed	The next sibling Element or null if none found.
	 */
	public function getNext($match = '')
	{
		foreach ($this->rgtSibling as $el)
		{
			if (!$match) {
				return $el;
			}
			elseif($el->getTag() == $match) {
				return $el;
			}
		}
		
		return null;
	}

	/**
	 * Like Element::getNext, but returns a collection of all the matched nextSiblings.
	 * 
	 */
	public function getAllNext()
	{
		return $this->rgtSibling;
	}

	/**
	 * Gets the first element that matches the passed in expression.
	 * 
	 * @param	string	$match	A comma seperated list of tag names to match the found element(s) with.
	 * @return	mixed	The first found element or null if none found.
	 */
	public function getFirst($match = '')
	{
		foreach ($this->children as $child)
		{
			if ($child instanceof TextNode || ($match && $child->getTag != $match)) continue;
			return $child;
		}

		return null;
	}

	/**
	 * Gets the last element that matches the passed in expression.
	 * 
	 * @param	string	$match	A comma seperated list of tag names to match the found element(s) with.
	 * @return	mixed	The last found element, or returns null if none found.
	 */
	public function getLast($match = '')
	{
		$reverse = array_reverse($this->children);
		foreach ($reverse as $child)
		{
			if ($child instanceof TextNode || ($match && $child->getTag != $match)) continue;
			return $child;
		}

		return null;
	}

	/**
	 * Works as Element::getPrevious, but tries to find the parentNode.
	 * 
	 * @param	string	$match	A tag name to match the found element(s) with.
	 * @return	mixed	The target Element's parent or null if no matching parent is found.
	 */
	public function getParent($match = '')
	{
		if (!$match) {
			return $this->parent;
		} else
		{
			$parent = $this->parent;
			while ($parent->getTag != $match)
			{
				if ($parent->hasParent) {
					$parent = $parent->getParent();
				} else {
					return null;
				}
			}
			return $parent;
		}
	}

	/**
	 * Like Element::getParent, but returns a collection of all the matched parentNodes up the tree
	 * 
	 * @param	string	$match	A tag name to match the found element(s) with.
	 * @return	mixed	A collection of all the matched parentNodes up the tree or null if no matching parent is found.
	 */
	public function getParents($match = '')
	{
		if (!$this->hasParent()) return null;

		$parent = $this->parent;
		$parents = new Elements;
		$parents->add($parent);
		while ($parent->hasParent)
		{
			if ($match && $parents->getTag != $match) continue;
			$parents->add($parent);
			$parent = $parent->getParent();
		}
		return $parents;
	}

	/**
	 * Like Element:getAllPrevious but returns all Element's previous and next siblings (excluding text nodes). Returns as Elements.
	 * 
	 * @param	string	$match	A tag name to match the found element(s) with.
	 * @return	object	A Elements object with all of the Element's siblings, except the text nodes.
	 */
	public function getSiblings($match = '')
	{
		$siblings = new Elements;
		foreach ($this->lftSibling as $lftSibling)
		{
			if ($match && $lftSibling->getTag != $match) continue;
			$siblings->add($lftSibling);
		}
		foreach ($this->rgtSibling as $rgtSibling)
		{
			if ($match && $rgtSibling->getTag != $match) continue;
			$siblings->add($rgtSibling);
		}
		
		return $siblings;
	}

	/**
	 * Returns all the Element's children (excluding text nodes). Returns as Elements.
	 * 
	 * @param	string	$match	A tag name to match the found element(s) with.
	 * @return	mixed	A Elements object with all of the Element's children, except the text nodes.
	 */
	public function getChildren($match = '')
	{
		if (!$match) return $this->children;
		$children = new Elements;
		foreach ($this->children as $child)
		{
			if ($child instanceof TextNode || $child->getTag != $match) continue;
			$children->add($child);
		}

		return $children;
	}

	/**
	 * Empties an Element of all its children.
	 * Obs: in mootools this function is called empty.
	 * 
	 * @return	object	This Element
	 */
	public function removeChildren()/*empty()*/
	{
		$this->children = new Elements;
		return $this;
	}
	
	/**
	 * Check if this Element has a child element.
	 * 
	 * @param	$id		string	An optional id to check for.
	 * @return	boolean	true if this element has a child, false otherwise.
	 * @deprecated	Use Element::contains() instead.
	 */
	public function hasChild($id = null)
	{
		return $this->contains($id);
	}
	
	/**
	 * Checks all descendants of this Element for a match.
	 * 
	 * @param	$id		string	An id to check for.
	 * @return	boolean	True if the element contains passed in Element is a child, otherwise false.
	 */
	public function contains($id)
	{
		if (empty($this->children)) return false;
		if (!empty($id))
		{
			foreach ($this->children as $child)
			{
				if ($child instanceof Element && $child->get('id') == $id) return true;
			}
			return false;
		}
		return (bool) count($this->children);
	}
	
	public function hasParent()
	{
		return ($this->parent instanceof Element);
	}

	//======================================//
	//										//
	//			Element.Style				//
	//										//
	//======================================//

	/**
	 * Sets a CSS property to the Element.
	 * 
	 * @param	string	$property	The property to set.
	 * @param	mixed	$value		The value to which to set it. Numeric values of properties requiring a unit will automatically be appended with 'px'.
	 * @return	object	This Element.
	 */
	public function setStyle($property, $value)
	{
		$this->attributes->styles[$property] = $value;
		return $this;
	}

	/**
	 * Applies a collection of styles to the Element.
	 * 
	 * @param	array	$styles	An associative array of property => value pairs for all the styles to apply.
	 * @return	object	This Element.
	 */
	public function setStyles($styles)
	{
		$this->attributes->styles = array_merge($this->attributes->styles, $styles);
		return $this;
	}

	/**
	 * Returns the style of the Element given the property passed in.
	 *
	 * @param	string	$property	The css style property you want to retrieve.
	 * @return	string	The style value.
	 */
	public function getStyle($property)
	{
		if (isset($this->attributes->styles[$property])) {
			return $this->attributes->styles[$property];
		}

		return null;
	}

	/**
	 * Returns an object of styles of the Element for each argument passed in.
	 *
	 * @param	string	$property	Any number of style properties.
	 * @return	array	An associative array of styles => value with the CSS styles.
	 */
	public function getStyles()
	{
		$array = array();
		$properties = func_get_args();
		foreach($properties as $property)
		{
			$array[(string)$property] = $this->getStyle($property);
		}

		return $array;
	}


	//======================================//
	//										//
	//			Element.Events				//
	//										//
	//======================================//

	/**
	 * Attaches an inline event listener to this element.
	 *
	 * @param	string	$name		The event name to monitor ('click', 'load', etc) without the prefix 'on'.
	 * @param	string	$function	The JavaScript function to execute.
	 * @return	object	This Element.
	 */
	public function addEvent($name, $function)
	{
		return $this->set('on'.ucfirst(strtolower($name)), $function);
	}

	/**
	 * Removes the specified inline event listener.
	 *
	 * @param	string	$name		The event name to monitor ('click', 'load', etc) without the prefix 'on'.
	 * @return	object	This Element.
	 */
	public function removeEvent($name)
	{
		return $this->removeProperty('on'.ucfirst(strtolower($name)));
	}
	
	/**
	 * The same as Element::addEvent, but accepts an array to add multiple events at once.
	 *
	 * @param	array	$events	An array with key/value representing: key the event name, and value the function that is called when the Event occurs.
	 * @return	object	This Element.
	 */
	public function addEvents($events)
	{
		foreach ($events as $name => $function)
		{
			$this->addEvent($name, $function);
		}
		return $this;
	}

	/**
	 * Removes all events of a certain type from an Element. If no argument is passed, removes all events of all types.
	 *
	 * @param	string	$events The event name (e.g. 'click'). Removes all events of that type.
	 * (array) An array of events names to remove.
	 * @return	object	This Element.
	 */
	public function removeEvents($events)
	{
		if (!is_array($events)) $events = array($events);
		foreach ($events as $event)
		{
			$this->removeEvent($event);
		}
		return $this;
	}

	/**
	 * used in set('events', array('click' => 'alert("clicked");'))
	 */
	public function setEvents($events)
	{
		return $this->addEvents($events);
	}


	//======================================//
	//										//
	//		End of Element.Events			//
	//										//
	//======================================//

	/**
	 * Set the tag of this Element.
	 *
	 * @param	string	$tag	Any xhtml valid tag.
	 * @return	object	This Element.
	 */
	protected function setTag($tag)
	{
		$this->tag = strtolower(trim($tag));
 		$this->inlineClose = in_array($tag, $this->inlineClose);
		return $this;
	}
	
	/**
	 * Returns the tag name of the Element in lower case.
	 *
	 * @return	string	The tag name in lower case.
	 */
	public function getTag()
	{
		return $this->tag;
	}
	
	
	/**
	 * Sets the inner text of the Element.
	 *
	 * @param	string	$text	The new text content for the Element.
	 * @return	object	This Element.
	 */
	public function setText($text)
	{
		$this->removeChildren();
		$this->children->add(new TextNode($text));
		return $this;
	}

	/**
	 * Gets the inner text of the Element.
	 * 
	 * @return	string	The text of the Element.
	 */
	public function getText()
	{
		$string = '';
		foreach ($this->children as $child)
		{
			if ($child instanceof TextNode) {
				$string .= (string)$child;
			} else {
				$string .= $child->getText();
			}
		}
		return $string;
	}

	/**
	 * Return a xhtml string of this element.
	 *
	 * @return string xhtml string of this element.
	 */
	protected function render()
	{
		$this->rendered = '<'.$this->getTag();
		$this->rendered .= (string) $this->attributes;
		if ($this->inlineClose)
		{
			$this->rendered .= '/>';
		}
		else
		{
			$this->rendered .= '>';
			if ($this->hasChild())
			{
				$children = $this->getChildren();
				foreach ($children as $child)
				{
					$this->rendered .= (string)$child;
				}
			}
			$this->rendered .= '</'.$this->getTag().'>';
		}

		return $this->rendered;
	}

	/**
	 * Return a xhtml string of this element.
	 *
	 * @return string xhtml string of this element.
	 */
	public function __toString()
	{
		return $this->render();
	}

	public static function doctype($type = 'xhtml-strict')
	{
		$dt = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD %s//EN" "http://www.w3.org/TR/%s.dtd">';
		switch ($type)
		{
			case 'html4-strict':
				$a = 'HTML 4.01';
				$b = 'html4/strict';
			case 'html4-trans':
				$a = 'HTML 4.01 Transitional';
				$b = 'html4/loose';
			case 'html4-frame':
				$a = 'HTML 4.01 Frameset';
				$b = 'html4/frameset';
			case 'xhtml-trans':
				$a = 'XHTML 1.0 Transitional';
				$b = 'xhtml1/DTD/xhtml1-transitional';
			case 'xhtml-frame':
				$a = 'XHTML 1.0 Frameset';
				$b = 'xhtml1/DTD/xhtml1-frameset';
			case 'xhtml11':
				$a = 'XHTML 1.1';
				$b = 'xhtml11/DTD/xhtml11';
			case 'xhtml-strict':
			default:
				$a = 'XHTML 1.0 Strict';
				$b = 'xhtml1/DTD/xhtml1-strict';
		}
		return sprintf($dt, $a, $b);
	}
}