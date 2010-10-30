<?php
$path = realpath(dirname(__FILE__));
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

require_once 'phFormViewElement.php';
require_once 'phFormException.php';
require_once 'phFormView.php';
require_once 'phData.php';

/**
 * This is the base class for all forms, a form can be something as simple as a reusable component
 * of provide a full set of functionality.
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 */
class phForm implements phFormViewElement, phData
{
	protected $_view;
	protected $_forms = array();
	protected $_bound = false;
	protected $_name = '';
	protected $_idFormat = '';
	protected $_nameFormat = '';
	protected $_valid = false;
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
		
		$this->configure();
	}
	
	/**
	 * A handy method subclasses can override to do specific setup
	 */
	public function configure()
	{
		
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
	
	public function hasForm($name)
	{
		return isset($this->_forms[$name]);
	}
	
	/**
	 * binds input from a post/get into the form, triggering validators and
	 * setting elements values
	 *  
	 * @param array $values array in $name=>$value format, may be multidimensional
	 * @param phForm $form the form that is being bound (null will mean $this is used)
	 * @todo remove the phForm arg
	 */
	public function bind($values)
	{	
		$this->_valid = false; // needs to be revalidated now it has been bound
		/*
		 * clear all existing values from our form, sub forms will clear themselves
		 * when their bind method is called
		 */
		$this->clear();
		
		/*
		 * Get all the elements and find a value in the posted array.  We do it this way around
		 * so we make sure all validators are fired regardless of if their value is posted or not
		 */
		$items = $this->_view->getAllData();
		
		foreach($items as $i)
		{
			$i->bind(
				isset($values[$i->getName()]) ? $values[$i->getName()] : null
			);
		}
		
		$this->setBound(true);
	}
	
	public function validate()
	{
		$this->_valid = true;
		
		$items = $this->_view->getAllData();
		foreach($items as $i)
		{
			if(!$i->validate())
			{
				$this->_valid = false;
			}
		}
		
		return $this->_valid;
	}
	
	
	public function bindAndValidate($values)
	{
		$this->bind($values);
		$this->validate();
	}
	
	public function isValid()
	{	
		if(!$this->isBound())
		{
			return false;
		}
		
		return $this->_valid;
	}
	
	public function clear()
	{
		$items = $this->_view->getAllData();
		foreach($items as $i)
		{
			$i->clear();
		}
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
		
		$items = $this->_view->getAllData();
		foreach($items as $i)
		{
			$errors = array_merge($errors, $i->getErrors());
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
	
	/**
	 * When a phform element in the view is found a phFormDataItem instance is created
	 * and then when, in bind it has its values set
	 * @see lib/form/phDataChangeListener::dataChanged()
	 */
	public function dataChanged(phFormDataItem $item)
	{
		$this->setValue($item->getValue());
	}
}
