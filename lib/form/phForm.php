<?php
/**
 * This is the base class for all forms, a form can be something as simple as a reusable component
 * of provide a full set of functionality.
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 */
class phForm
{
	protected $view;
	protected $forms = array();
	
	public function __construct($template)
	{
		$this->view = new phFormView($template);
	}
		
	public function addForm($name, phForm $form)
	{
		if($this->entityExists($name))
		{
			throw new phFormException("An entity with the name '{$name}' already exists");
		}
		
		$this->forms[$name] = $form;
	}
	
	protected function entityExists($name)
	{
		return (isset($this->$name) || isset($this->forms[$name]));
	}
	
	public function __get($name)
	{
		$entity = null;
		
		if(isset($this->forms[$name]))
		{
			$entity = $this->forms[$name];
		}
		else
		{
			$entity = $this->view->$name;
		}
		
		if($entity===null)
		{
			throw new phFormException("The entity '{$name}' is not on this form");
		}
	}
}