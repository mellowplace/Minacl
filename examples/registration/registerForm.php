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
?>
<dl>
	<dt>
		<label for="<?php echo $this->id('name'); ?>">Your name:</label>
	</dt>
	<dd>
		<?php echo $this->errorList('fullname'); ?>
		<input 	type="text" 
				id="<?php echo $this->id('name'); ?>" 
				name="<?php echo $this->name('fullname'); ?>" />
	</dd>
	
	<dt>
		<label for="<?php echo $this->id('email'); ?>">Email address:</label>
	</dt>
	<dd>
		<?php echo $this->errorList('email'); ?>
		<input 	type="text" 
				id="<?php echo $this->id('email'); ?>"
				name="<?php echo $this->name('email'); ?>" />
	</dd>
	
	<dt>
		<label for="<?php echo $this->id('password'); ?>">Password:</label>
	</dt>
	<dd>
		<?php echo $this->errorList('password'); ?>
		<input 	type="password" 
				id="<?php echo $this->id('password'); ?>"
				name="<?php echo $this->name('password'); ?>" />
	</dd>
	
	<dt>
		<label for="<?php echo $this->id('confirmPassword'); ?>">Confirm password:</label>
	</dt>
	<dd>
		<?php echo $this->errorList('confirmPassword'); ?>
		<input 	type="password" 
				id="<?php echo $this->id('confirmPassword'); ?>"
				name="<?php echo $this->name('confirmPassword'); ?>" />
	</dd>
	
	<dt>&nbsp;</dt>
	<dd>
		<input type="submit" value="Register" />
	</dd>
</dl>