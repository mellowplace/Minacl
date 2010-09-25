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