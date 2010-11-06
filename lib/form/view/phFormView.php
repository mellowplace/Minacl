<?php
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
	/**
	 * Holds all the phFormDataItem objects on the view
	 * @var array
	 */
	protected $_dataItems = array();
	/**
	 * Holds the document type declaration which allows us to include
	 * definitions for xhtml entities like &nbsp; that would be unparsable
	 * otherwise
	 * @var string
	 */
	protected $_docTypeDecl = '';
	
	public function __construct($template, phForm $form)
	{
		$this->_template = $template;
		$this->_form = $form;
		
		$this->_docTypeDecl = file_get_contents(realpath(dirname(__FILE__) . '/../') . DIRECTORY_SEPARATOR . 'dtd' . DIRECTORY_SEPARATOR . 'xhtml-entities.ent', false);
		if($this->_docTypeDecl===false)
		{
			throw new phFormException('Cannot find xhtml entities definitions');
		}
		
		$this->_dom = $this->parseDom($template);
	}
	
	protected function parseDom($view)
	{
		ob_start();
		echo "<!DOCTYPE phformdoc [\n";
		/*
		 * include the xhtml entity definitions so there are
		 * no parsing errors when coming across special entities
		 * like &nbsp;
		 */
		echo $this->_docTypeDecl;
		echo "\n]>\n";
		echo "<phformdoc>\n";
		require phViewLoader::getInstance()->getViewFileOrStream($view);
		echo "</phformdoc>\n";
		
		$xml = ob_get_clean();
		
		try 
		{
			$dom = new SimpleXMLElement($xml);
			return $dom;
		}
		catch(Exception $e)
		{
			throw new phFormException("Unparsable html in view '{$view}', parser said: {$e->getMessage()}");
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
		$xml = str_replace('<phformdoc>', '', $xml);
		$xml = str_replace('</phformdoc>', '', $xml);
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
	public function __get($name)
	{
		$this->initialize();
		
		if(!isset($this->_dataItems[$name]))
		{
			throw new phFormException("The data item \"{$name}\" is not registered in this view", $code);
		}
		
		return $this->_dataItems[$name];
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
	 * Parses the view and sets up the data items and elements that appear there
	 * 
	 * @throws phFormException
	 */
	protected function initialize()
	{
		if(isset($this->_initialized))
		{
			return;
		}
		
		foreach($this->_names as $rewrittenName=>$name)
		{
			$elements = $this->getDom()->xpath("//*[@name='{$rewrittenName}']");
			
			if(!sizeof($elements))
			{
				throw new phFormException("No elements found with the name of '{$name}'");
			}
			
			if($this->_form->hasForm($name))
			{
				$dataItem = $this->_form->getForm($name);
			}
			else
			{
				$dataItem = new phFormDataItem($name);
			}
			
			$this->_dataItems[$name] = $dataItem;
			
			foreach($elements as $element)
			{
				$f = phElementFactory::getFactory($element);
				if($f===null)
				{
					throw new phFormException("no factory exists for handling the '{$element->getName()}' element");
				}
				
				$phElement = $f->createPhElement($element, $this);
				if($phElement instanceof phDataChangeListener)
				{
					$dataItem->addChangeListener($phElement);
				}
				
				$this->_elements[$this->getRealId((string)$element->attributes()->id)] = $phElement;
			}
		}
		
		$this->_initialized = true;
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
		
		return $this->_dataItems;
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
		$key = array_search($name, $this->_names);
		if($key!==false)
		{
			return $key;
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
		$errors = array();
		
		if($this->getDom())
		{
			$e = $this->$id;
			$errors = $e->getErrors();
		}
		
		return $errors;
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
	 * @param string $id optional id to get only errors from one field
	 */
	public function errorList($id = null)
	{
		if($this->getDom())
		{
			$errors = $id===null ? $this->allErrors() : $this->error($id);
			$html = sizeof($errors)>0 ? '<ul>' : '';
			foreach($errors as $e)
			{
				$html .= '<li>'.htmlentities($e,ENT_COMPAT,'utf-8').'</li>';
			}
			$html .= sizeof($errors)>0 ? '</ul>' : '';
			
			return $html;
		}
	}
}
