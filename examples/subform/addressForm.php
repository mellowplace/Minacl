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
 * address form template for the sub-forms example
 * (http://minacl.org/learn-by-example/74-3-using-sub-forms.html)
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage examples.subform
 */
?>
<dt>Address</dt>
<dd>
	<input type="text" id="<?php echo $this->id('address') ?>" name="<?php echo $this->name('address') ?>" />
</dd>
<dt>City</dt>
<dd>
	<input type="text" id="<?php echo $this->id('city') ?>" name="<?php echo $this->name('city') ?>" />
</dd>
<dt>Zip/Postal Code</dt>
<dd>
	<input type="text" id="<?php echo $this->id('zip') ?>" name="<?php echo $this->name('zip') ?>" />
</dd>