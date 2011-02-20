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
	<dt>Username</dt>
	<dd>
		<input 	type="text" 
				id="<?php echo $this->id('username'); ?>" 
				name="<?php echo $this->name('username'); ?>" 
				value="" />
	</dd>
	<dt>Password</dt>
	<dd>
		<input 	type="password" 
				id="<?php echo $this->id('password'); ?>" 
				name="<?php echo $this->name('password'); ?>" 
				value="" />
	</dd>
</dl>

<input 	type="text" 
		id="<?php echo $this->id('noValue'); ?>" 
		name="<?php echo $this->name('noValue'); ?>" />

<input 	type="checkbox" 
		id="<?php echo $this->id('checkbox1'); ?>"
		name="<?php echo $this->name('checkbox[0]'); ?>"
		value="1" checked="checked" />

<input 	type="checkbox" 
		id="<?php echo $this->id('checkbox2'); ?>"
		name="<?php echo $this->name('checkbox[1]'); ?>"
		value="2" />
		
<input 	type="checkbox" 
		id="<?php echo $this->id('checkbox3'); ?>"
		name="<?php echo $this->name('checkbox[2]'); ?>"
		value="3" />