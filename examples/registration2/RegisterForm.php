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
 * Second example from Minacl.org 
 * (http://minacl.org/learn-by-example/72-2-making-the-form-more-usable-a-re-usable.html)
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage examples.registration2
 */
class RegisterForm extends phForm
{
	public function postInitialize()
	{
		$this->fullname->setValidator(new phRequiredValidator(array(
											phRequiredValidator::REQUIRED => 'Please enter your full name'
										)));
		$this->email->setValidator(new phRequiredValidator(array(
											phRequiredValidator::REQUIRED => 'Please enter your email address'
										)));
		$this->password->setValidator(new phRequiredValidator(array(
											phRequiredValidator::REQUIRED => 'Please enter your password'
										)));
		$this->confirmPassword->setValidator(new phCompareValidator(
											$this->password, 
											phCompareValidator::EQUAL,
											array (
												phCompareValidator::INVALID => 'The passwords do not match'
											)
										));
	}
}