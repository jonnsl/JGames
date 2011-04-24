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
class Elements implements Iterator
{
	private $elements = array();

	public function __construct($array = null)
	{
		if (is_array($array)) {
            $this->elements = $array;
        }
    }
	
	public function __call($name, $arguments)
	{
		if (method_exists('Element', $name))
		{
			foreach ($this as $el)
			{
				call_user_func_array(array($el, $name), $arguments);
			}
			$this->rewind();
		}
		return $this;
	}
	
	public function __invoke($el)
	{
		return $this->add($el);
	}
	
	public function add($el, $where = 'bottom')
	{
		switch ($where)
		{
			case 'top':
				array_unshift($this->elements, $el);
			case 'bottom':
			default:
				$this->elements[] = $el;
		}
		return $this;
	}

	public function remove(Element $el)
	{
		foreach ($this->elements as $k => $element)
		{
			if ($element === $el) unset($this->elements[$k]);
		}
		return $this;
	}

	public function rewind()
	{
		reset($this->elements);
	}

	public function current()
	{
		return current($this->elements);
	}

	public function key() 
	{
		return key($this->elements);
	}

	public function next() 
	{
		return next($this->elements);
	}

	public function valid()
	{
		return is_int(key($this->elements));
	}
}