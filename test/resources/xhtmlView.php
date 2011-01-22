<?php 
/*
 * phForms Project: An HTML forms library for PHP
 *          https://github.com/mellowplace/PHP-HTML-Driven-Forms/
 * Copyright (c) 2010, 2011 Rob Graham
 * 
 * This file is part of phForms.
 *
 * phForms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as 
 * published by the Free Software Foundation, either version 3 of 
 * the License, or (at your option) any later version.
 *
 * phForms is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public 
 * License along with phForms.  If not, see 
 * <http://www.gnu.org/licenses/>.
 */
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>Test document</title>
	</head>
	<body>
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
	</body>
</html>