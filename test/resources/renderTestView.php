<span>Login Form</span>
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
<!-- Some comments here -->
<table border="0" width="100%">
	<tr>
		<td>Table test</td>
	</tr>
</table>


<input 	type="checkbox" 
		id="<?php echo $this->id('checkbox1'); ?>"
		name="<?php echo $this->name('checkbox'); ?>"
		value="1" />

<input 	type="checkbox" 
		id="<?php echo $this->id('checkbox2'); ?>"
		name="<?php echo $this->name('checkbox'); ?>"
		value="2" />
		
<input 	type="checkbox" 
		id="<?php echo $this->id('checkbox3'); ?>"
		name="<?php echo $this->name('checkbox'); ?>"
		value="3" />
		
<!-- should be ok with HTML comments and entities below -->
&nbsp;&gt;&lt;&quot;