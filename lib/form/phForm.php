<?php
$path = realpath(dirname(__FILE__));
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

require_once 'phElement.php';
require_once 'phFormException.php';
require_once 'phFormView.php';
/**
 * This is the base class for all forms, a form can be something as simple as a reusable component
 * of provide a full set of functionality.
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 */
class phForm implements phElement
{
	protected $_view;
	protected $_forms = array();
	protected $_bound = false;
	protected $_name = '';
	protected $_idFormat = '';
	protected $_nameFormat = '';
	/**
	 * Holds a list of errors that have been added to the form
	 * @var array
	 */
	protected $_errors = array();
	
	public function __construct($name, $template)
	{
		if(!$this->isValidId($name))
		{
			throw new phFormException("Invalid form name '{$name}'", $code);
		}
		
		$this->_name = $name;
		$this->setNameFormat($name . '[%s]');
		$this->setIdFormat($name . '_%s');
		
		$this->_view = new phFormView($template, $this);
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
		$form->setIdFormat(sprintf($this->getNameFormat(), $name) . '_%s');
		$this->_forms[$name] = $form;
	}
	
	public function getForm($name)
	{
		if(!isset($this->_forms[$name]))
		{
			throw new phFormException("no form with the name '{$name}' exists");
		}
		
		return $this->_forms[$name];
	}
	
	/**
	 * binds input from a post/get into the form, triggering validators and
	 * setting elements values
	 *  
	 * @param array $values array in $name=>$value format, may be multidimensional
	 * @param phForm $form the form that is being bound (null will mean $this is used)
	 */
	public function bind($values, phForm $form=null)
	{	
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
			$elements = $this->_view->getElementsFromName($postedName);
			foreach($elements as $e)
			{
				$e->bind($v, $this);
			}
		}
		
		$this->setBound(true);
	}
	
	public function isValid()
	{	
		$elements = $this->_view->getAllElements();
		foreach($elements as $e)
		{
			if(!$e->isValid())
			{
				return false;
			}
		}
		
		return true;
	}
	
	public function clearValues()
	{
		$elements = $this->_view->getAllElements();
		foreach($elements as $e)
		{
			$e->clearValues();
		}
	}
	
	/**
	 * Gets the cleaned values of the form
	 */
	public function getValues()
	{
		$elements = $this->_view->getAllElements();
		$values = array();
		foreach($elements as $e)
		{
			$values[] = $e->getValues();
		}
		
		return $values;
	}
	
	/**
	 * Get errors actually gets ALL errors in the form, that is errors that have
	 * been added to this object (global errors) as well as all child element errors
	 * 
	 * @return array all the errors in this form
	 */
	public function getErrors()
	{
		$errors = $this->_errors;
		
		$elements = $this->_view->getAllElements();
		foreach($elements as $e)
		{
			$errors = array_merge($errors, $e->getErrors());
		}
		
		return $errors;
	}
	
	/**
	 * This returns just the errors that have been added to this form
	 * 
	 * @return array array of error strings
	 */
	public function getGlobalErrors()
	{
		return $this->_errors;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/phElement::addError()
	 */
	public function addError($message)
	{
		$this->_errors[] = $message;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/phElement::resetErrors()
	 */
	public function resetErrors()
	{
		$this->errors = array();
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
	
	public function setIdFormat($format)
	{
		$this->_idFormat = $format;
	}
	
	public function getIdFormat()
	{
		return $this->_idFormat;
	}
	
	public function isValidId($id)
	{
		return preg_match('/^[a-zA-Z\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $id)>0;
	}
	
	public function __get($name)
	{
		return $this->_view->$name;
	}
	
	public function __toString()
	{
		return $this->_view->render();
	}
	
	protected function entityExists($name)
	{
		return (isset($this->$name) || isset($this->_forms[$name]));
	}
}
