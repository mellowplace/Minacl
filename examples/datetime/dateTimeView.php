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
 * DateTime view from the Minacl examples 
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage examples.datetime
 */
?>
<select id="<?php echo $this->id('day'); ?>" name="<?php echo $this->name('day'); ?>">
<?php 
for($d=1; $d<=31; $d++):
	$ds = $d;
	if(strlen($ds)==1)
	{
		$ds = '0' . $ds;
	}
?>
	<option value="<?php echo $ds; ?>"><?php echo $ds; ?></option>
<?php 
endfor;
?>
</select> /
<select id="<?php echo $this->id('month'); ?>" name="<?php echo $this->name('month'); ?>">
<?php 
foreach($months as $m=>$desc):
?>
	<option value="<?php echo $m; ?>"><?php echo $desc; ?></option>
<?php 
endforeach;
?>
</select> /
<select id="<?php echo $this->id('year'); ?>" name="<?php echo $this->name('year'); ?>">
<?php 
foreach($years as $y):
?>
	<option value="<?php echo $y; ?>"><?php echo $y; ?></option>
<?php 
endforeach;
?>
</select>&nbsp;
<select id="<?php echo $this->id('hour'); ?>" name="<?php echo $this->name('hour'); ?>">
<?php 
for($h=0; $h<=23; $h++):
	$hs = $h;
	if(strlen($hs)==1)
	{
		$hs = '0' . $hs;
	}
?>
	<option value="<?php echo $hs; ?>"><?php echo $hs; ?></option>
<?php 
endfor;
?>
</select>:
<select id="<?php echo $this->id('minute'); ?>" name="<?php echo $this->name('minute'); ?>">
<?php 
for($m=0; $m<=59; $m++):
	$ms = $m;
	if(strlen($ms)==1)
	{
		$ms = '0' . $ms;
	}
?>
	<option value="<?php echo $ms; ?>"><?php echo $ms; ?></option>
<?php 
endfor;
?>
</select>:
<select id="<?php echo $this->id('second'); ?>" name="<?php echo $this->name('second'); ?>">
<?php 
for($s=0; $s<=59; $s++):
	$ss = $s;
	if(strlen($ss)==1)
	{
		$ss = '0' . $ss;
	}
?>
	<option value="<?php echo $ss; ?>"><?php echo $ss; ?></option>
<?php 
endfor;
?>
</select>