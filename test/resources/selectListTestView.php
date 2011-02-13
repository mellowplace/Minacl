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
<select id="<?php echo $this->id('options'); ?>" name="<?php echo $this->name('options[]'); ?>" multiple="multiple">
	<option value="A">Option A</option>
	<option value="B">Option B</option>
	<option value="C">Option C</option>
</select>


<select id="<?php echo $this->id('single'); ?>" name="<?php echo $this->name('single'); ?>">
	<option value="A">Option A</option>
	<option value="B">Option B</option>
	<option value="C">Option C</option>
</select>