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
 * For the sub-forms example
 * (http://minacl.org/learn-by-example/74-3-using-sub-forms.html)
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage examples.subform
 */
require_once dirname(__FILE__) . '/../../lib/form/phLoader.php';
phLoader::registerAutoloader();
phViewLoader::setInstance(new phFileViewLoader(dirname(__FILE__)));

/*
 * create the main form
 */
$form = new phForm('deliver', 'deliveryForm');
/*
 * create the address form, the first argument
 * needs to match what we have in the form helper
 * on the deliveryForm template
 */
$address = new phForm('address', 'addressForm');
/*
 * add the sub form to the main form
 */
$form->addForm($address);

if($_SERVER['REQUEST_METHOD']=='POST'):
	/*
	 * data has been posted back, bind it to the form
	 */
	$form->bindAndValidate($_POST['deliver']);

	if($form->isValid()):
?>
		<h1>Thanks!</h1>
		<p>We will deliver to 
		<?php echo $form->address->address . ', ' . $form->address->city . ', ' . $form->address->zip ?></p>
<?php
	endif;
endif;
?>
<form action="/subform/deliver.php" method="post">
	<?php echo $form; ?>
	<input type="submit" value="Deliver me!" />
</form>
