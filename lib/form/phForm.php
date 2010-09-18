<?php
/**
 * This is the base class for all forms, a form can be something as simple as a reusable component
 * of provide a full set of functionality.
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 */
class phForm implements phBindable
{
	protected $_view;
	protected $_forms = array();
	protected $_valid = false;
	protected $_bound = false;
	protected $_name = '';
	protected $_nameFormat = '';
	
	public function __construct($name, $template)
	{
		if(!$this->isNameValid($name))
		{
			throw new phFormException("Invalid form name '{$name}'", $code);
		}
		
		$this->_name = $name;
		$this->setNameFormat($name . '[%s]');
		$this->_view = new phFormView($template);
	}
	
	public function addForm(phForm $form)
	{
		$name = $form->getName();
		
		if($this->entityExists($name))
		{
			throw new phFormException("An entity with the name '{$name}' already exists");
		}
		
		/*
		 * setup the name format for the sub form
		 */
		$form->setNameFormat(sprintf($this->getNameFormat(), $name) . '[%s]');
		$this->_forms[$name] = $form;
	}
	
	/**
	 * binds input from a post/get into the form, triggering validators and
	 * setting elements values
	 *  
	 * @param array $values array in $name=>$value format, may be multidimensional
	 */
	public function bind($values)
	{
		if($this->isBound())
		{
			throw new phFormException('This form has already been bound');
		}
		
		/*
		 * clear all existing values from our form, sub forms will clear themselves
		 * when their bind method is called
		 */
		$this->clearValues();
		
		/*
		 * set the values of the form into the elements (element could also be a form)
		 */
		foreach($values as $postedName=>$v)
		{
			$name = $this->_view->getRealIdFromName($postedName);
			$this->$name->bind($v);
		}
	}
	
	public function isValid()
	{
		if(!$this->_bound)
		{
			return false;
		}
	}
	
	public function clearValues()
	{
		$elements = $this->_view->getAllElements();
		foreach($elements as $e)
		{
			$e->clearValue();
		}
	}
	
	public function isBound()
	{
		return $this->_bound;
	}
	
	protected function setBound($bound)
	{
		$this->_bound = $bound;
	}
	
	public function getName()
	{
		return $this->_name;
	}
	
	public function setNameFormat($format)
	{
		$this->_nameFormat = $format;
	}
	
	public function getNameFormat()
	{
		return $this->_nameFormat;
	}
	
	public function __get($name)
	{
		$entity = null;
		
		if(isset($this->_forms[$name]))
		{
			$entity = $this->_forms[$name];
		}
		else
		{
			$entity = $this->_view->$name;
		}
		
		if($entity===null)
		{
			throw new phFormException("The entity '{$name}' is not on this form");
		}
	}
	
	protected function entityExists($name)
	{
		return (isset($this->$name) || isset($this->_forms[$name]));
	}
	
	protected function isNameValid($name)
	{
		return true;
	}
}