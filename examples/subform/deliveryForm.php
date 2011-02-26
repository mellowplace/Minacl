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
 * delivery form for the sub-forms example
 * (http://minacl.org/learn-by-example/74-3-using-sub-forms.html)
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage examples.subform
 */
 ?>
<dl>
	<dt>First Name</dt>
	<dd>
		<input type="text" id="<?php echo $this->id('firstName') ?>" name="<?php echo $this->name('firstName') ?>" />
	</dd>
	<dt>Surname</dt>
	<dd>
		<input type="text" id="<?php echo $this->id('surname') ?>" name="<?php echo $this->name('surname') ?>" />
	</dd>
	<?php echo $this->form('address') ?>
</dl>