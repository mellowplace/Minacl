<?php
require_once 'validator/phValidator.php';
require_once 'validator/phValidatorException.php';
/**
 * A class that allows you to chain validators together using add, or, andNot and orNot
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage validator
 */
class phValidatorLogic implements phValidator
{
	protected $_validators = array();
	
	protected static $_AND = 1;
	protected static $_OR = 2;
	
	public function __construct(phValidator $validator)
	{
		$this->_validators[] = $validator;
	}
	
	/**
	 * Validate the chain executing the correct logic for joining each validator
	 * @see lib/form/validator/phValidator::validate()
	 */
	public function validate($value, phValidatable $errors)
	{
		$valid = null;
		
		foreach($this->_validators as $v)
		{
			if($valid===null)
			{
				$valid = $v->validate($value, $errors);
			}
			else
			{
				$logic = $v[1];
				
				if($logic==self::$_AND)
				{
					$valid = $valid && $v[0]->validate($value, $errors);
				}
				else
				if($logic==self::$_OR)
				{
					$valid = $valid || $v[0]->validate($value, $errors);
				}
			}
		}
		
		return $valid;
	}
	
	/**
	 * chain a validator with AND logic
	 * @param phValidator $validator
	 * @return phValidatorLogic
	 */
	public function and_(phValidator $validator)
	{
		$this->_validators[] = array($validator, self::$_AND);
		return $this;
	}
	
	/**
	 * chain a validator with OR logic
	 * @param phValidator $validator
	 * @return phValidatorLogic
	 */
	public function or_(phValidator $validator)
	{
		$this->_validators[] = array($validator, self::$_OR);
		return $this;
	}
}