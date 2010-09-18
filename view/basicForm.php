<dl>
	<dt>User Name</dt>
	<dd>
		<?php echo $this->error('username'); ?>
		<input type="text" name="<?php echo $this->name('username'); ?>" value="<?php echo $user->getUsername(); ?>" />
	</dd>
</dl>
<?php echo $this->form('calendar'); ?>