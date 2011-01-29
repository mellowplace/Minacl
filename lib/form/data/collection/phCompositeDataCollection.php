<?php
/*
 * phForms Project: An HTML forms library for PHP
 *          https://github.com/mellowplace/PHP-HTML-Driven-Forms/
 * Copyright (c) 2010, 2011 Rob Graham
 *
 * This file is part of phForms.
 *
 * phForms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of
 * the License, or (at your option) any later version.
 *
 * phForms is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with phForms.  If not, see
 * <http://www.gnu.org/licenses/>.
 */

/**
 * This class is a sort of dynamic composite, each time register is called
 * with a new type of phFormViewElement if there is no collection to store
 * that instance it calls phFormViewElement->createDataCollection and adds
 * a new phDataCollection class to the composite.
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage data.collection
 */
class phCompositeDataCollection implements phDataCollection
{
	protected $_collections = array();
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/data/collection/phDataCollection::register()
	 */
	public function register(phFormViewElement $element, phNameInfo $name)
	{
		$elementClass = get_class($element);
		if(!array_key_exists($elementClass, $this->_collections))
		{
			$this->_collections[$elementClass] = $element->createDataCollection(); 
		}
		
		$collection = $this->_collections[$elementClass];
		$collection->register($element, $name);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/data/collection/phDataCollection::createIterator()
	 */
	public function createIterator()
	{
		$i = new phMultipleIterator();
		foreach($this->_collections as $c)
		{
			$i->addIterator($c->createIterator());
		}
		
		return $i;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/data/collection/phDataCollection::find()
	 */
	public function find($name)
	{
		foreach($this->_collections as $c)
		{
			$dataItem = $c->find($name);
			if($dataItem!==null)
			{
				return $dataItem;
			}
		}
		
		return null;
	}
}

/**
 * Iterator alot like the SPL MultipleIterator (>=5.3 only though I think)
 * just iterates over multiple Iterator instances
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage data.collection
 */
class phMultipleIterator implements Iterator
{
	protected $_iterators = array();
	protected $_iteratorIndex = null;
	protected $_currentIterator = null;
	
	public function addIterator(Iterator $i)
	{
		$this->_iterators[] = $i;
	}
	
	 public function current()
	 {
	 	if($this->_currentIterator!==null)
	 	{
	 		return $this->_currentIterator->current();
	 	}
	 }

	 public function key()
	 {
	 	if($this->_currentIterator!==null)
	 	{
	 		return $this->_currentIterator->key();
	 	}
	 }

	 public function next()
	 {
	 	if($this->_currentIterator!==null)
	 	{
	 		$this->_currentIterator->next();
	 		if(!$this->_currentIterator->valid() && $this->_iteratorIndex<(sizeof($this->_iterators)-1))
	 		{
	 			$this->_iteratorIndex++;
	 			$this->_currentIterator = $this->_iterators[$this->_iteratorIndex];
	 			$this->_currentIterator->rewind();
	 		}
	 	}
	 }
	 
	 public function rewind()
	 {
	 	if(sizeof($this->_iterators)>0)
	 	{
		 	$this->_currentIterator = $this->_iterators[0];
		 	$this->_currentIterator->rewind();
	 	}
	 }
	 
	 public function valid()
	 {
	 	return ($this->_currentIterator!==null && $this->_currentIterator->valid());
	 }
}