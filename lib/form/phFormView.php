<?php
require_once('phForm.php');
require_once('factory/phElementFactory.php');
require_once 'phFormException.php';
/**
 * This class is responsible for rendering the form and providing an API to access
 * its elements
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 */
class phFormView
{
	/**
	 * The owning form instance
	 * @var phForm
	 */
	protected $_form = null;
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
	
	protected $_elements = array();
	
	public function __construct($template, phForm $form)
	{
		$this->_template = $template;
		$this->_form = $form;
		$this->_dom = $this->parseDom($template);
	}
	
	protected function parseDom($template)
	{
		ob_start();
		require($template);
		$xml = '<xhtml>' . ob_get_clean() . '</xhtml>';
		
		try 
		{
			$dom = new SimpleXMLElement($xml);
			return $dom;
		}
		catch(Exception $e)
		{
			throw new phFormException("Unparsable html in template '{$template}'");
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
		/*
		 * parse again so any errors are drawn, then for each element,
		 * replace with our instance
		 */
		$dom = dom_import_simplexml($this->parseDom($this->_template));
		$xpath = new DOMXPath($dom->ownerDocument);
		
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
		}
		
		
		$xml = $dom->ownerDocument->saveXML();
		/*
		 * get rid of the xml declaration and wrapping xhtml tags that were
		 * needed to create a dom from the view but have no use in being returned
		 */
		$xml = str_replace('<?xml version="1.0"?>', '', $xml);
		$xml = str_replace('<xhtml>', '', $xml);
		$xml = str_replace('</xhtml>', '', $xml);
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
	 * Searches the template for an element with the $id and returns a reference to the decorated
	 * element object
	 * 
	 * @param string $id
	 */
	public function __get($id)
	{
		if(!isset($this->_elements[$id]))
		{
			$rewrittenId = $this->getRewrittenId($id);
			$elements = $this->_dom->xpath("//*[@id='{$rewrittenId}']");
			
			if(!sizeof($elements))
			{
				throw new phException("No element found by the id of '{$id}'");
			}
			
			$element = $elements[0];
			
			$f = phElementFactory::getFactory($element);
			if($f===null)
			{
				throw new phFormException("no factory exists for handling the '{$element->getName()}' element");
			}
			
			$phElement = $f->createPhElement($element, $this);
			
			$this->_elements[$id] = $phElement;
		}
		
		return $this->_elements[$id];
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
	
	/*
	 * The element grabber functions
	 */
	
	/**
	 * Gets an array of elements from the posted variable name
	 * @param string $name
	 * @return array an array of phElement objects
	 * @todo We've searched the document once with xpath and have the SimpleXmlElement, we should not search it again in __get, needs an intermediary method that both this and __get can use
	 */
	public function getElementsFromName($name)
	{
		$name = $this->getRewrittenName($name);
		$elements = $this->_dom->xpath("//*[@name='{$name}']");
		
		if(!sizeof($elements))
		{
			throw new phFormException("No elements found by the name of '{$name}'", $code);
		}
		
		$phElements = array();
		/*
		 * return the initialised instances by using the magic __get function
		 */
		foreach($elements as $e)
		{
			$id = (string)$e->attributes()->id;
			if(!$id)
			{
				throw new phFormException("Element with the name '{$name}' has no id set");
			}
			
			$id = $this->getRealId($id);
			$phElements[] = $this->$id;
		}
		
		return $phElements;
	}
	
	public function getAllElements()
	{
		$elements = array();
		
		foreach($this->_ids as $id)
		{
			$elements[] = $this->$id;
		}
		
		return $elements;
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
		if(isset($this->_ids[$id]))
		{
			return $this->_ids[$id];
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
		if(isset($this->_names[$name]))
		{
			return $this->_names[$name];
		}
		else
		{
			$newName = sprintf($this->_form->getNameFormat(), $name);
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
	 * Returns any errors for the element with $id
	 * 
	 * @param string $id
	 * @return array list of errors
	 */
	public function error($id)
	{
		if($this->_dom)
		{
			$e = $this->$id;
			return $e->getErrors();
		}
	}
	
	/**
	 * Gets all errors for the form
	 */
	public function allErrors()
	{
		if($this->_dom)
		{
			return $this->_form->getErrors();
		}
	}
	
	/**
	 * Gets an html <ul> list of errors
	 * 
	 * @param string $id optional id to get only errors from one field
	 */
	public function errorList($id = null)
	{
		if($this->_dom)
		{
			$errors = $id===null?$this->allErrors():$this->error($id);
			$html = "<ul>";
			foreach($errors as $e)
			{
				$html .= "<li>".htmlentities($e,ENT_COMPAT,'utf-8')."</li>";
			}
			$html .= "</ul>";
			
			return $html;
		}
	}
}
