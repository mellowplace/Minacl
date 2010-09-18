<?php
require_once('phForm.php');
require_once('phElementFactory.php');
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
		$html = $this->getDom()->asXml();	
		return $html;
	}
	
	/**
	 * Searches the template for an element with the $id and returns the decorated
	 * element object
	 * 
	 * @param string $id
	 */
	public function __get($id)
	{
		if(!isset($this->_elements[$id]))
		{
			$rewrittenId = $this->getRewrittenId($id);
			$elements = $this->getElementsByXpath("//*[@id='{$rewrittenId}']");
			$element = $elements[0];
			
			$this->_elements[$id] = $element;
		}
		
		return $this->_elements[$id];
	}
	
	public function __toString()
	{
		return $this->render();
	}
	
	protected function isValidId($id)
	{
		return preg_match('/^[a-zA-Z\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $id)>0;
	}
	
	/**
	 * Gets the real element id from the rewritten one
	 * 
	 * @param string $id
	 */
	protected function getRealId($id)
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
	protected function getRewrittenId($id)
	{
		$key = array_search($id, $this->_ids);
		if($key===false)
		{
			throw new phFormException("No elements on the form had their id rewritten to '{$id}'");
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
	protected function getRewrittenName($name)
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
	 */
	public function getElementsFromName($name)
	{
		$name = $this->getRewrittenName($name);
		return $this->getElementsByXpath("//*[@name='{$name}']");
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
	
	protected function getElementsByXpath($xpath)
	{
		$elements = $this->_dom->xpath($xpath);
		if($elements===false || !sizeof($elements))
		{
			throw new phFormException("no elements found using xpath '{$xpath}' in the template '{$this->_template}'");
		}
		
		$phElements = array();
		
		foreach($elements as $element)
		{
			$f = phElementFactory::getFactory($element);
			if($f===null)
			{
				throw new phFormException("no factory exists for handling the '{$element->getName()}' element");
			}
			
			$phElements[] = $f->createPhElement($element, $this->_form);
		}
		
		return $phElements;
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
			if(!$this->isValidId($id))
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
		return "<phform name=\"{$name}\" />";
	}
}