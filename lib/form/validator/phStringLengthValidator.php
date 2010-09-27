<?php
require_once 'validator/phValidatorCommon.php';
require_once 'validator/phValidatorException.php';
/**
 * A validator that makes sure a string is over a min length, under a max length
 * or in between the two
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage validator
 */
class phStringLengthValidator extends phValidatorCommon
{
	const INVALID = 1;
	
	private $_min = null, $_max = null;
	
	public function validate($value, phValidatable $errors)
	{
		if(is_array($value))
		{
			throw new phValidatorException('I cannot validate elements that return multiple values');
		}
		
		$length = strlen($value);
		
		$valid = true;
		
		if($this->_min!==null && $length<$this->_min)
		{
			$valid = false;
		}
		else if($this->_max!==null && $length>$this->_max)
		{
			$valid = false;
		}
		
		if(!$valid)
		{
			$errors->addError($this->getError(self::INVALID));
		}
		
		return $valid;
	}
	
	/**
	 * sets the minimum character length
	 * @param integer $num
	 * @return phStringLengthValidator
	 */
	public function min($num)
	{
		$this->_min = $num;
		return $this;
	}
	
	/**
	 * sets the maximum character length
	 * @param integer $num
	 * @return phStringLengthValidator
	 */
	public function max($num)
	{
		$this->_max = $num;
		return $this;
	}
	
	protected function getValidErrorCodes()
	{
		return array(self::INVALID);
	}
	
	protected function getDefaultErrorMessages()
	{
		$message = 'Incorrect length for this value';
		return array(self::INVALID=>$message);
	}
}