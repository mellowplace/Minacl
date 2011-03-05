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
 * A class that allows you to chain validators together using add, or, andNot and orNot
 * 
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage validator
 */
class phValidatorLogic implements phValidator
{
	protected $_validators = array();
	
	const _AND = 1;
	const _OR = 2;
	
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
		$valid = '';
		
		foreach($this->_validators as $v)
		{
			if($valid==='')
			{
				$valid = $v->validate($value, $errors) ? 'true' : 'false';
			}
			else
			{
				$logic = $v[1];
				
				if($logic==self::_AND)
				{
					$valid .= ' && ' . ($v[0]->validate($value, $errors) ? 'true' : 'false');
				}
				else if($logic==self::_OR)
				{
					$valid .= ' || ' . ($v[0]->validate($value, $errors) ? 'true' : 'false');
				}
			}
		}
		
		eval('$result = ' . $valid . ';');
		return $result;
	}
	
	/**
	 * chain a validator with AND logic
	 * @param phValidator $validator
	 * @return phValidatorLogic
	 */
	public function and_(phValidator $validator)
	{
		$this->_validators[] = array($validator, self::_AND);
		return $this;
	}
	
	/**
	 * chain a validator with OR logic
	 * @param phValidator $validator
	 * @return phValidatorLogic
	 */
	public function or_(phValidator $validator)
	{
		$this->_validators[] = array($validator, self::_OR);
		return $this;
	}
}