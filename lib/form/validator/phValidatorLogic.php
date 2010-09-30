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
	
	public function __construct(phValidator $validator, $not = false)
	{
		if($not)
		{
			// invert the validator
			$validator = new phNotValidator($validator);
		}
		
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
	 * chain a validator with AND NOT logic
	 * @param phValidator $validator
	 * @return phValidatorLogic
	 */
	public function andNot(phValidator $validator)
	{
		$this->_validators[] = array(new phNotValidator($validator), self::$_AND);
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
	
	
	/**
	 * chain a validator with OR NOT logic
	 * @param phValidator $validator
	 * @return phValidatorLogic
	 */
	public function orNot(phValidator $validator)
	{
		$this->_validators[] = array(new phNotValidator($validator), self::$_OR);
		return $this;
	}
	
}

/**
 * The not validator wraps a phValidator object and inverts its result by applying
 * NOT logic
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage validator
 */
class phNotValidator implements phValidator
{
	/**
	 * @var phValidator
	 */
	protected $_validator = null;
	
	public function __construct(phValidator $validator)
	{
		$this->_validator = $validator;
	}
	
	public function validate($value, phValidatable $errors)
	{
		$tempErrors = new phTemporaryErrorContainer();
		/*
		 * invert the valid value
		 */
		$valid = !$this->_validator->validate($value, $tempErrors);
		
		if(!$valid)
		{
			$errorMessages = $tempErrors->getErrors();
			foreach($errorMessages as $e)
			{
				$errors->addError($e);
			}
		}
		
		return $valid;
	}
}

/**
 * The temporary error container is used by the phNotValidator to store errors
 * this is so errors are not added when actually the not inverts the result so
 * the validator passes
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage validator
 */
class phTemporaryErrorContainer implements phValidatable
{
	protected $_errors = array();
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/phValidatable::addError()
	 */
	public function addError($message)
	{
		$this->_errors[] = $message;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/phValidatable::resetErrors()
	 */
	public function resetErrors()
	{
		$this->_errors = array();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/phValidatable::getErrors()
	 */
	public function getErrors()
	{
		return $this->_errors;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/phValidatable::validate()
	 */
	public function validate()
	{
		return sizeof($this->_errors)==0;
	}
}