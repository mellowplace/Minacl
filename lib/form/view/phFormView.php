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
 * This class is responsible for rendering the form and providing an API to access
 * its elements
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage view
 */
class phFormView
{
	/**
	 * The owning form instance
	 * @var phForm
	 */
	protected $_form = null;
	/**
	 * The template this view will render
	 * @var string
	 */
	protected $_template = null;
	/**
	 * Dom handler for the html in the template
	 * @var SimpleXMLElement
	 */
	protected $_dom = null;
	/**
	 * Holds the list of rewritten ids
	 * @var array
	 */
	protected $_ids = array();
	/**
	 * Holds the list of rewritten input names
	 * @var array
	 */
	protected $_names = array();
	/**
	 * For every registered name this array stores the name only part
	 * e.g. "ids" if it was "ids[]" as the key and true if the name is
	 * an array, false otherwise.  It is used in the name() function to
	 * stop people mixing array names with normal names.
	 * @var array
	 */
	protected $_types = array();
	/**
	 * An array holding all the elements on the view
	 * @var array
	 */
	protected $_elements = array();
	/**
	 * Holds all the phFormDataItem objects on the view
	 * @var phFormDataCollection
	 */
	protected $_dataCollection = array();
	/**
	 * Holds the document type declaration which allows us to include
	 * definitions for xhtml entities like &nbsp; that would be unparsable
	 * otherwise
	 * @var string
	 */
	protected $_docTypeDecl = '';
	/**
	 * Holds any custom vars set for the view
	 * @var array
	 */
	protected $_customVars = array();
	/**
	 * @var bool true if the view has been initialised
	 */
	protected $_initialized = false;
	
	public function __construct($template, phForm $form)
	{
		$this->_template = $template;
		$this->_form = $form;
		
		$this->_docTypeDecl = file_get_contents(realpath(dirname(__FILE__) . '/../') . DIRECTORY_SEPARATOR . 'dtd' . DIRECTORY_SEPARATOR . 'xhtml-entities.ent', false);
		if($this->_docTypeDecl===false)
		{
			throw new phFormException('Cannot find xhtml entities definitions');
		}
	}
	
	protected function parseDom($_view)
	{
		/*
		 * All the variables in this method start with _ (underscore)
		 * so they don't conflict with any custom vars that need to be
		 * passed through to the view (which we will build up now)
		 */
		foreach($this->_customVars as $_name=>$_value)
		{
			${$_name} = $_value;
		}
		/*
		 * Now include the template and catch it's output
		 */
		ob_start();
		echo "<!DOCTYPE phformdoc [\n";
		/*
		 * include the xhtml entity definitions so there are
		 * no parsing errors when coming across special entities
		 * like &nbsp;
		 */
		echo $this->_docTypeDecl;
		echo "\n]>";
		echo "<phformdoc>";
		require phViewLoader::getInstance()->getViewFileOrStream($_view);
		echo "</phformdoc>";
		
		$_xml = ob_get_clean();
		
		try 
		{
			$_dom = new SimpleXMLElement($_xml);
			return $_dom;
		}
		catch(Exception $_e)
		{
			throw new phFormException("Unparsable html in view '{$_view}', parser said: {$_e->getMessage()}");
		}
	}
	
	/**
	 * gets the DOM tree for the template
	 * @return SimpleXMLElement
	 */
	protected function getDom()
	{
		return $this->_dom;
	}
	
	/**
	 * Using the dom, render out the fully parsed content of the form
	 */
	public function render()
	{
		$this->initialize();
		/*
		 * parse again so any errors are drawn, then for each element,
		 * replace with our instance
		 */
		$dom = dom_import_simplexml($this->parseDom($this->_template));
		$xpath = new DOMXPath($dom->ownerDocument);
		
		$forms = array();
		
		$elements = $this->getAllElements();
		foreach($elements as $e)
		{
			if($e instanceof phSimpleXmlElement)
			{
				$newElement = $e->getElement();
				$id = (string)$newElement->attributes()->id;
				
				$nodes = $xpath->query("//*[@id='{$id}']");
				$oldElement = $nodes->item(0);
				
				$new = $dom->ownerDocument->importNode(dom_import_simplexml($newElement), true);
				$oldElement->parentNode->replaceChild($new, $oldElement);
			}
			else if($e instanceof phForm)
			{
				$forms[$e->getName()] = $e->__toString();
			}
		}
		
		$dom->ownerDocument->removeChild($dom->ownerDocument->doctype);
		$xml = $dom->ownerDocument->saveXML();
		/*
		 * get rid of the xml declaration and wrapping xhtml tags that were
		 * needed to create a dom from the view but have no use in being returned
		 */
		$xml = str_replace('<?xml version="1.0"?>', '', $xml);
		/*
		 * trim the xml here as SimpleXmlElement seems to add on new lines
		 * at the beginning and end
		 */
		$xml = trim($xml);
		$xml = str_replace("<phformdoc>", '', $xml);
		$xml = str_replace("</phformdoc>", '', $xml);
		/*
		 * replace any phform tags with their relevant subform output
		 */
		foreach($forms as $name=>$html)
		{
			$id = $this->id($name);
			$xml = preg_replace('/<phform .*id="'.$id.'".*\/>/', $html, $xml);
		}
		
		return $xml;
	}
	 
	/**
	 * @return phForm the form this view is for
	 */
	public function getForm()
	{
		return $this->_form;
	}
	
	/**
	 * returns a reference to the phFormDataItem that represents the input denoted by $name
	 * 
	 * @param string $name
	 */
	public function getData($name)
	{
		$this->initialize();
		
		$info = new phNameInfo($name);
		$dataItem = $this->_dataCollection->find($info->getName());
		
		if($dataItem===null)
		{
			throw new phFormException("The data item \"{$name}\" is not registered in this view", $code);
		}
		
		return $dataItem;
	}
	
	/**
	 * Gets the real element id from the rewritten one
	 * 
	 * @param string $id
	 */
	public function getRealId($id)
	{
		if(!isset($this->_ids[$id]))
		{
			throw new phFormException("No elements on the form where registered with the id of '{$id}'");
		}
		
		return $this->_ids[$id];
	}
	
	/**
	 * Gets a rewritten id from an original one
	 * 
	 * @param $id
	 * @return string the rewritten id
	 */
	public function getRewrittenId($id)
	{
		$key = array_search($id, $this->_ids);
		if($key===false)
		{
			throw new phFormException("No element by the id of '{$id}' was registered on the form");
		}
		
		return $key;
	}
	
	/**
	 * Gets a rewritten name from an original one
	 * 
	 * @param string $name
	 * @throws phFormException
	 * @return string the rewritten name
	 */
	public function getRewrittenName($name)
	{
		$key = array_search($name, $this->_names);
		if($key===false)
		{
			throw new phFormException("No elements on the form had their name rewritten to '{$name}'");
		}
		
		return $key;
	}
	
	/**
	 * Gets a real name from a rewritten one
	 * 
	 * @param string $rewrittenName
	 * @throws phFormException
	 */
	public function getRealName($rewrittenName)
	{
		if(!isset($this->_names[$rewrittenName]))
		{
			throw new phFormException("No elements had their name rewritten to '{$rewrittenName}'");
		}
		
		return $this->_names[$rewrittenName];
	}
	
	/**
	 * Has this view been initialised already?
	 * @return boolean
	 */
	public function isInitialized()
	{
		return $this->_initialized;
	}
	
	/**
	 * Parses the view and sets up the data items and elements that appear there
	 * 
	 * @throws phFormException
	 */
	protected function initialize()
	{
		if($this->_initialized)
		{
			return;
		}
		
		/*
		 * before initialising call the forms
		 * preInitialize method so it can setup
		 * any custom vars for the view
		 */
		$this->_form->preInitialize();
		
		$dom = $this->parseDom($this->_template);
		
		$this->_dataCollection = new phCompositeDataCollection();
		
		foreach($this->_names as $rewrittenName=>$name)
		{
			$nameInfo = new phNameInfo($name);
			$elements = $dom->xpath("//*[@name='{$rewrittenName}']");
			
			if(!sizeof($elements))
			{
				throw new phFormException("No elements found with the name of '{$name}'");
			}
			
			$phElements = array();
			foreach($elements as $element)
			{
				if(!strlen((string)$element->attributes()->id))
				{
					throw new phFormException("You must specify an id for the element with name '{$nameInfo->getName()}'");
				}
				
				$f = phElementFactory::getFactory($element);
				if($f===null)
				{
					$realId = $this->getRealId((string)$element->attributes()->id);
					throw new phFormException("no factory exists for handling the element with ID '{$realId}' which is of the type '{$element->getName()}'");
				}
				
				$phElement = $f->createPhElement($element, $this);
				
				$this->_elements[$this->getRealId((string)$element->attributes()->id)] = $phElement;
				
				$this->_dataCollection->register($phElement, $nameInfo);
			}
		}
		
		$this->_dom = $dom;
		
		$this->_initialized = true;
		
		/*
		 * now we are initialised call the forms postInitialize method so it
		 * can do any setup like adding validators
		 */
		$this->_form->postInitialize();
	}
	
	/*
	 * The element grabber functions
	 */
	public function getAllElements()
	{
		$this->initialize();
		
		return $this->_elements;
	}
	
	/**
	 * Gets an element identified by $id
	 * @param $id
	 * @throws phFormException if the element does not exist
	 * @return phFormViewElement
	 */
	public function getElement($id)
	{
		$this->initialize();
		
		if(!isset($this->_elements[$id]))
		{
			throw new phFormException("The element referenced by '{$id}' could not be found");
		}
		
		return $this->_elements[$id];
	}
	
	/**
	 * @return array array of phData objects
	 */
	public function getAllData()
	{
		$this->initialize();
		
		return $this->_dataCollection->createIterator();
	}
	
	/*
	 * Begin render helper methods
	 */
	/**
	 * Gets a rewritten id that will be unique even with sub forms
	 *
	 * @param string $id
	 */
	public function id($id)
	{
		$key = array_search($id, $this->_ids);
		if($key!==false)
		{
			return $key;
		}
		else
		{
			if(!$this->_form->isValidId($id))
			{
				throw new phFormException("'{$id}' is not valid, ids must be a-z0-9 or '_' only and contain no spaces and must not start with an '_' (underscore) or number", $code);
			}

			$newId = sprintf($this->_form->getIdFormat(), $id);
			$this->_ids[$newId] = $id;

			return $newId;
		}
	}
	
	/**
	 * Gets a rewritten name that will be unique and relate to the form
	 * that the element belongs to
	 * 
	 * @param string $name
	 */
	public function name($name)
	{
		/*
		 * valid names can only start with alpha characters
		 * following the first letter can be alpha, numeric or underscores (but need'nt be present, a valid name can be 1 character)
		 * the second parahenthises check, if an array is present that it is valid
		 */
		$nameInfo = new phNameInfo($name);
		if(!$nameInfo->isValid())
		{
			throw new phFormException("'{$name}' is not valid, names must be a-z0-9 or '_' only and contain no spaces and must not start with an '_' (underscore) or number");
		}
		
		$nameOnly = $nameInfo->getName();
		/*
		 * check if someone is trying to specify 2 names where one is an array and the other isn't
		 * e.g. name="address" and name="address[zip]"
		 */
		if(array_key_exists($nameOnly, $this->_types) && $nameInfo->isArray() != $this->_types[$nameOnly])
		{
			throw new phFormException("Invalid name {$name}, trying to mix array's and normal types");
		}
			
		$key = array_search($name, $this->_names);
		if($key!==false)
		{
			return $key;
		}
		else
		{
			$this->_types[$nameOnly] = $nameInfo->isArray();
			
			$newName = sprintf($this->_form->getNameFormat(), $nameOnly);
			if($nameInfo->isArray())
			{
				$newName .= $nameInfo->getArrayKeyString();
			}
				
			$this->_names[$newName] = $name;
			
			return $newName;
		}
	}
	
	/**
	 * Registers that there is a form in the view, also returns a tag which is later 
	 * replaced with the actual form content
	 * 
	 * @param string $name the name of the form
	 */
	public function form($name)
	{
		$newId = $this->id($name);
		$newName = $this->name($name);
		return "<phform id=\"{$newId}\" name=\"{$newName}\" />";
	}
	
	/**
	 * Returns any errors for the data identified by $name
	 * 
	 * @param string $name
	 * @return array list of errors
	 */
	public function error($name)
	{
		$errors = array();
		
		if($this->getDom())
		{
			$e = $this->getData($name);
			$errors = $e->getErrors();
		}
		
		$messages = array();
		foreach($errors as $e)
		{
			$messages[] = $e->getMessage();
		}
		
		return $messages;
	}
	
	/**
	 * Gets all errors for the form
	 */
	public function allErrors()
	{
		if($this->getDom())
		{
			return $this->_form->getErrors();
		}
	}
	
	/**
	 * Gets an html <ul> list of errors
	 * 
	 * @param string $name optional id to get only errors from one data item
	 */
	public function errorList($name = null)
	{
		if($this->getDom())
		{
			$errors = $name===null ? $this->allErrors() : $this->error($name);
			$html = sizeof($errors)>0 ? '<ul>' : '';
			foreach($errors as $e)
			{
				$html .= '<li>'.htmlentities($e, ENT_COMPAT, 'utf-8').'</li>';
			}
			$html .= sizeof($errors)>0 ? '</ul>' : '';
			
			return $html;
		}
	}
	
	public function __set($name, $value)
	{
		if(substr($name, 0, 1)==='_')
		{
			throw new phFormException("Invalid custom view variable '{$name}'.  Names starting with '_' are reserved.");
		}
		
		$this->_customVars[$name] = $value;
	}
	
	public function __get($name)
	{
		if(array_key_exists($name, $this->_customVars))
		{
			return $this->_customVars[$name];
		}

		$trace = debug_backtrace();
		trigger_error(
            'Undefined property in view: ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
			E_USER_NOTICE
		);
		
		return null;
	}
}
