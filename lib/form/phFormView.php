<?php
/**
 * This class is responsible for rendering the form and providing object access to
 * its elements
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 */
class phFormView
{
	protected $_form = null;
	protected $_template = null;
	protected $_dontRenderForms = false;
	/**
	 * Dom handler for the html in the template
	 * @var SimpleXMLElement
	 */
	protected $_dom = null;
	
	protected $_elements = array();
	
	public function __construct($template, phForm $form)
	{
		$this->_template = $template;
		$this->_form = $form;
		$this->_dom = $this->getDom();
	}
	
	protected function getDom()
	{
		$xml = $this->render(true);
		try 
		{
			$dom = new SimpleXMLElement($xml);
			return $dom;
		}
		catch(Exception $e)
		{
			throw new phFormException("Unparsable html in template '{$this->_template}'");
		}
	}
	
	/**
	 * Returns the fully parsed content of the form
	 * 
	 * @param boolean $noForms if we only want html from the view and not subforms then this should be true
	 */
	public function render($noForms = false)
	{
		$this->_dontRenderForms = $noForms;
		
		ob_start();
		require($this->_template);
		$html = ob_get_clean();
		
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
	 * Begin render helper methods
	 */
	
	/**
	 * Outputs a form in the view
	 * 
	 * @param string $name the name of the form
	 */
	public function form($name)
	{
		if($this->_dontRenderForms)
		{
			return; // quietly return, we are not rendering forms
		}
		
		return $form->$name->__toString();
	}
}