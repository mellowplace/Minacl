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
<tr>
	<td>
		<label for="<?php echo $this->id('address'); ?>">Address:</label>
	</td>
	<td>
		<input 
			type="text" 
			id="<?php echo $this->id('address'); ?>" 
			name="<?php echo $this->name('address'); ?>"
			value="" />
	</td>
</tr>
<tr>
	<td>
		<label for="<?php echo $this->id('postalCode'); ?>">Post Code:</label>
	</td>
	<td>
		<input 
			type="text" 
			id="<?php echo $this->id('postalCode'); ?>" 
			name="<?php echo $this->name('postal_code'); ?>"
			value="" />
	</td>
</tr>