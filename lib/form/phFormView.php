<?php
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
		$xml = ob_get_clean();
		
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
	 * Searches the template for an element with an id of $name and returns the decorated
	 * element object
	 * 
	 * @param string $name
	 */
	public function __get($name)
	{
		if(!isset($this->_elements[$name]))
		{
			$id = $this->getRealId($name);
			$element = $this->_dom->xpath("//*[@id='{$id}']");
			if(!$element)
			{
				throw new phFormException("no element with the id '{$id}' found in the template '{$this->_template}'");
			}
			
			$f = phElementFactory::getFactory($element);
			if($f===null)
			{
				throw new phFormException("no factory exists for handling the '{$element->getName()}' element");
			}
			
			$this->_elements[$name] = $f->createPhElement($element);
		}
		
		return $this->_elements[$name];
	}
	
	public function __toString()
	{
		return $this->render();
	}
	
	/*
	 * The id grabber functions
	 */
	
	/**
	 * Gets the elements id from the posted variable name
	 * @param string $name
	 */
	public function getRealIdFromName($name)
	{
		
	}
	
	/**
	 * Gets the real element id from the rewritten one
	 * @param string $id
	 */
	public function getRealId($id)
	{
		
	}
	
	/*
	 * Begin render helper methods
	 */
	
	/**
	 * Gets a rewritten id that will be unique even with sub forms
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
			$newId = sprintf($this->_form->getIdFormat(), $id);
			$this->_ids[$newId] = $id;
		
			return $newId;
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