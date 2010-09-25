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