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
 * A validator that checks for a correctly formatted email address as per RFC 
 * 5322 (mostly section 3.4.1) and RFC 5321 - EXCEPT - it only allows public
 * addresses, i.e. this validator will fail something like "rob@localhost"
 * 
 * Please note that this will not validate internationalised email addresses as
 * per RFC 4952
 *
 * @author Nathan Whitworth <nathan@nathanwhitworth.co.uk>
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage validator
 */
class phEmailValidator extends phValidatorCommon
{
	const INVALID = 1;
	
	/**
	 * RG - 
	 * Based on a regex used in filter_var (http://svn.php.net/viewvc/php/php-src/trunk/ext/filter/logical_filters.c)
	 * (copyright notice below) which is in turn based on a regex by Michael (copyright notice below)
	 * 
	 * Copyright (c) 1997-2010 The PHP Group
	 * Copyright © Michael Rushton 2009-10
	 * http://squiloople.com/
	 * Feel free to use and redistribute this code. But please keep this copyright notice. 
	 * 
	 * @var string
	 */
	protected $_emailRegex = "/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD";
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/validator/phValidator::validate()
	 */
	public function validate($value, phValidatable $errors)
	{
		if(is_array($value))
		{
			throw new phValidatorException('I cannot validate elements that return multiple values');
		}

		$valid = $this->isValidEmail($value);

		if(!$valid)
		{
			$errors->addError($this->getError(self::INVALID));
		}

		return $valid;
	}

	protected function isValidEmail($email)
	{
		$isValid = true;
		$atIndex = strrpos($email, "@");
		if(is_bool($atIndex) && !$atIndex)
		{
			$isValid = false;
		}
		else
		{
			$domain = substr($email, $atIndex+1);
			$local = substr($email, 0, $atIndex);
			$localLen = strlen($local);
			$domainLen = strlen($domain);
			
			if($localLen < 1 || $localLen > 64)
			{
				// local part length exceeded
				$isValid = false;
			}
			else if($domainLen < 1 || $domainLen > 255)
			{
				// domain part length exceeded
				$isValid = false;
			}
			else if(!preg_match($this->_emailRegex, $email))
			{
				// bad email format
				$isValid = false;
			}
		}
		
		return $isValid;
	}
	
	protected function getValidErrorCodes()
	{
		return array(self::INVALID);
	}

	protected function getDefaultErrorMessages()
	{
		return array(self::INVALID=>'Invalid email address');
	}
}