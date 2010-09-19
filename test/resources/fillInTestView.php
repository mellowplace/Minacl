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

<input 	type="checkbox" 
		id="<?php echo $this->id('checkbox1'); ?>"
		name="<?php echo $this->name('checkbox'); ?>"
		value="1" checked="checked" />

<input 	type="checkbox" 
		id="<?php echo $this->id('checkbox2'); ?>"
		name="<?php echo $this->name('checkbox'); ?>"
		value="2" />
		
<input 	type="checkbox" 
		id="<?php echo $this->id('checkbox3'); ?>"
		name="<?php echo $this->name('checkbox'); ?>"
		value="3" />