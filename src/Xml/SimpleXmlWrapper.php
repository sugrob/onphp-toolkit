<?php

namespace OnPHPToolkit\Xml;

class SimpleXMLWrapper extends \SimpleXMLIterator {

	/**
	 * @return bool
	 */
	public function hasAttributes() 
	{
		return (bool)$this->attributes();
	}

	/**
	 * @return array
	 */
	public function getAttributes() 
	{
		$attributes = array();

		if ($this->hasAttributes()) {
			foreach ($this->attributes() as $key => $val) {
				$attributes[$key] = (string)$val;
			}
		}

		return $attributes;
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasAttribute($name) 
	{
		$attributes = $this->getAttributes();
		return array_key_exists($name, $attributes);
	}

	/**
	 * @param string $name
	 * @return string
	 */
	public function getAttribute($name) 
	{
		$attributes = $this->getAttributes();
		return array_key_exists($name, $attributes) ? $attributes[$name] : null;
	}

	/**
	 * @return array
	 */
	public function getChildren() 
	{
		if (!$this->hasChildren())
				return array();

		$children = array();

		foreach ($this->children() as $name => $child) {
			$children[] = $child;
		}

		return $children;
	}

	/**
	 * @return array
	 */
	public function hasChildren() 
	{
		return $this->isExist() && (bool)$this->children();
	}

	/**
	 * @return boolean
	 */
	public function isExist() 
	{
		return (bool)$this;
	}
	
	/**
	 * @return string
	 */
	public function toString()
	{
		return $this->hasChildren()	? $this->asXML() : (string) $this;
	}
	
	public function toBoolean()
	{
		if ($this->isExist()) {
			$value = strtolower((string)$this);
			if ($value == '') 
				return null;
			
			return ($value == 1 || $value == 'true');
		}
		
		return null;
	}
}