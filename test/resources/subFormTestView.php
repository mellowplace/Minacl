<table width="100%">
	<thead>
		<tr>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
				<label for="<?php echo $this->id('firstName'); ?>">First Name:</label>
			</td>
			<td>
				<input type="text" 
					id="<?php echo $this->id('firstName'); ?>" 
					name="<?php echo $this->name('first_name'); ?>"
					value="" />
			</td>
		</tr>
		<?php echo $this->form('address'); ?>
	</tbody>
</table>