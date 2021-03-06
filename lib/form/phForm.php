<?php
/*
 * Minacl Project: An HTML forms library for PHP
 *          https://github.com/mellowplace/PHP-HTML-Driven-Forms/
 * Copyright (c) 2010, 2011 Rob Graham
 * 
 * This file is part of Minacl.
 *
 * Minacl is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as 
 * published by the Free Software Foundation, either version 3 of 
 * the License, or (at your option) any later version.
 *
 * Minacl is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public 
 * License along with Minacl.  If not, see 
 * <http://www.gnu.org/licenses/>.
 */

/**
 * This is the base class for all forms, a form can be something as simple as a reusable component
 * of provide a full set of functionality.
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 */
class phForm implements phFormViewElement, phData
{
	/**
	 * @var phFormView
	 */
	protected $_view;
	protected $_forms = array();
	protected $_bound = false;
	protected $_name = '';
	protected $_idFormat = '';
	protected $_nameFormat = '';
	protected $_elementFinder = null;
	protected $_valid = false;
	/**
	 * Holds a list of errors that have been added to the form
	 * @var array
	 */
	protected $_errors = array();
	/**
	 * Holds an optional validator to validate this form with
	 * @var phValidator
	 */
	protected $_validator = null;
	/**
	 * An object that gets validators from the data items on the view 
	 * @var phValidatorFinder
	 */
	protected $_validatorFinder = null;
	
	/**
	 * @param string $name the name of this form (if you use it as a subform you can access it with this name)
	 * @param string $template the name of the template to use
	 * @param array $viewParams optional variables to pass on to the view (the array key becomes the variable name)
	 * 
	 * @throws phFormException
	 */
	public function __construct($name, $template, array $viewParams = array())
	{
		if(!$this->isValidId($name))
		{
			throw new phFormException("Invalid form name '{$name}'", $code);
		}
		
		$this->_name = $name;
		$this->setNameFormat($name . '[%s]');
		$this->setIdFormat($name . '_%s');
		
		$this->_view = new phFormView($template, $this, $viewParams);
		$this->_elementFinder = new phElementFinder($this->_view);
		$this->_validatorFinder = new phValidatorFinder($this->_view);
	}
	
	/**
	 * A handy method, called by the view prior to initialisation, that allows the
	 * form to setup any custom vars that the view might need to render 
	 */
	public function preInitialize()
	{
		
	}
	
	/**
	 * A handy method subclasses can override to do specific setup, it's called by the view
	 * once the form is initialised.  This is so if you are using subforms the form isn't
	 * initialised to early.
	 */
	public function postInitialize()
	{
		
	}
	
	public function addForm(phForm $form)
	{
		/*
		 * check the form has not been initialised, if it has then we
		 * must throw an exception because the names will be written
		 * wrong
		 */
		if($form->isInitialized())
		{
			throw new phFormException("The form {$form->getName()} has already been initialized.  You must add a subform straight after creation.");
		}
		
		$name = $form->getName();
		
		if($this->entityExists($name))
		{
			throw new phFormException("An entity with the name '{$name}' already exists");
		}
		
		/*
		 * setup the name format for the sub form
		 */
		$form->setNameFormat(sprintf($this->getNameFormat(), $name) . '[%s]');
		$form->setIdFormat(sprintf($this->getIdFormat(), $name) . '_%s');
		$this->_forms[$name] = $form;
	}
	
	/**
	 * Gets a subform
	 * 
	 * @param string $name
	 * @return phForm
	 * @throws phFormException
	 */
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
	 * @return phFormView the view for this form
	 */
	public function getView()
	{
		return $this->_view;
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
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/data/phData::getValue()
	 */
	public function getValue()
	{
		/*
		 * build the values up from what we have in our
		 * sub forms and data items
		 */
		$items = $this->_view->getAllData();
		$value = array();
		foreach($items as $i)
		{
			$value[$i->getName()] = $i->getValue();
		}
		
		return $value;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/phValidatable::validate()
	 */
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
		
		/*
		 * if this form itself has a validator, execute it
		 * and check it too passes
		 */
		$v = $this->getValidator();
		if($v && !$v->validate($this->getValue(), $this))
		{
			$this->_valid = false;
		}
		
		return $this->_valid;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/data/phData::setValidator()
	 */
	public function setValidator(phValidator $validator)
	{
		$this->_validator = $validator;
		
		return $validator;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/data/phData::getValidator()
	 */
	public function getValidator()
	{
		return $this->_validator;
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
		/*
		 * merge in element errors
		 */
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
	public function addError(phValidatorError $message)
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
		/*
		 * go through any subforms and set the name format
		 * again incase ours has changed in a way that affects them
		 */
		foreach($this->_forms as $form)
		{
			$form->setNameFormat(sprintf($this->getNameFormat(), $form->getName()) . '[%s]');
		}
	}
	
	public function getNameFormat()
	{
		return $this->_nameFormat;
	}
	
	public function setIdFormat($format)
	{
		$this->_idFormat = $format;
		/*
		 * go through any subforms and set the id format
		 * again incase ours has changed in a way that affects them
		 */
		foreach($this->_forms as $form)
		{
			$form->setIdFormat(sprintf($this->getIdFormat(), $form->getName()) . '_%s');
		}
	}
	
	public function getIdFormat()
	{
		return $this->_idFormat;
	}
	
	/**
	 * Returns true if the forms view has been initialised
	 * @return boolean
	 */
	public function isInitialized()
	{
		return $this->_view->isInitialized();
	}
	
	public function isValidId($id)
	{
		return preg_match('/^[a-zA-Z\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $id)>0;
	}
	
	public function __get($name)
	{
		return $this->_view->getData($name);
	}
	
	/**
	 * @return phElementFinder an object that finds elements in the view
	 */
	public function element()
	{
		return $this->_elementFinder;
	}
	
	/**
	 * @return phValidatorFinder an object that gets the validator set on a data item
	 */
	public function validator()
	{
		return $this->_validatorFinder;
	}
	
	public function __toString()
	{
		/*
		 * exceptions cannot be thrown from __toString() and if they are then PHP catches them
		 * and rethrows a generic one.  This leads to confusion as to what caused the original
		 * error, the solution is to use trigger error and describe the exception instead.
		 */
		try
		{
			$html = $this->_view->render();
			return $html;
		}
		catch(Exception $e)
		{
			$class = get_class($e);
			trigger_error("{$class} exception thrown in phForm::__toString()\nMessage: {$e->getMessage()}\nCode: {$e->getCode()}", E_USER_ERROR);
			/*
			 * still return a string, because if a custom error handler is set then it may be that
			 * trigger_error above will not fatal
			 */
			return '';
		}
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
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/phFormViewElement::bindDataItem()
	 */
	public function bindDataItem(phFormDataItem $item)
	{
		/*
		 * don't want to do anything, the passed phFormDataItem will
		 * be this very same object
		 */
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/phFormViewElement::createDataCollection()
	 */
	public function createDataCollection()
	{
		return new phFormDataCollection(); 
	}
}

/**
 * Private class used by phForm for finding elements in the view.
 * (it is returned from the element() function)
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 */
class phElementFinder
{
	/**
	 * @var phFormView
	 */
	protected $_view = null;
	
	public function __construct(phFormView $view)
	{
		$this->_view = $view;
	}
	
	/**
	 * Finds the element in the view referenced by $id
	 * @param string $id
	 */
	public function __get($id)
	{
		return $this->_view->getElement($id);
	}
}

/**
 * Private class that finds validators by the data item they are set on.
 * (it is returned from phForm::validator())
 * @author rob
 *
 */
class phValidatorFinder
{
	/**
	 * @var phFormView
	 */
	protected $_view = null;
	
	public function __construct(phFormView $view)
	{
		$this->_view = $view;
	}
	
	/**
	 * Finds a validator attached to the data item in the view referenced by $name
	 * @param string $name
	 * @return phData
	 */
	public function __get($name)
	{
		$data = $this->_view->getData($name);
		return $data->getValidator();
	}
}