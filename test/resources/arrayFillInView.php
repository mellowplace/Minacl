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
<!-- Test some array named checkboxes first -->
<input type="checkbox" id="<?php echo $this->id('ids_1')?>" name="<?php echo $this->name('ids[]') ?>" value="1" />
<input type="checkbox" id="<?php echo $this->id('ids_2')?>" name="<?php echo $this->name('ids[]') ?>" value="2" />
<input type="checkbox" id="<?php echo $this->id('ids_3')?>" name="<?php echo $this->name('ids[]') ?>" value="3" />

<input type="text" id="<?php echo $this->id('test_name') ?>" name="<?php echo $this->name('test[name]') ?>" value="" />

<textarea id="<?php echo $this->id('test_address') ?>" name="<?php echo $this->name('test[address]') ?>"></textarea>