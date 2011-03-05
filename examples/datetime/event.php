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
 * Event script from the Minacl examples
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage examples.datetime
 */
/*
 * usual setup code
 */
require_once dirname(__FILE__) . '/../../lib/form/phLoader.php';
phLoader::registerAutoloader();
phViewLoader::setInstance(new phFileViewLoader(dirname(__FILE__)));
/*
 * also need to include our custom class phDateTime
 */
require_once 'phDateTime.php';

$form = new phForm('event', 'eventView');
/*
 * last 2 params say the only available year should
 * be 2011
 */
$timeForm = new phDateTime('time', 'dateTimeView', 2011, 2011);
/*
 * Add the datetime form as a subform to the master event
 * form.
 */
$form->addForm($timeForm);

/*
 * add validators to the time so the date time chosen must be between
 * the start and end of January
 */
$errorMessage = array(phCompareValidator::INVALID => 'The event must occur sometime in January');
/*
 * setup a validator that says the datetime value given must be greater
 * or equal to the start of January 2011
 */
$start = new phCompareValidator(strtotime('2011-01-01 00:00:00'), phCompareValidator::GREATER_EQUAL, $errorMessage);
/*
 * setup a validator that says the datetime value given must be less
 * or equal to the end of January 2011
 */
$end = new phCompareValidator(strtotime('2011-01-31 23:59:59'), phCompareValidator::LESS_EQUAL, $errorMessage);
/*
 * combine the start and end validators so the date must be between
 * the two values
 */
$between = new phValidatorLogic($start);
$between->and_($end);
$timeForm->setValidator($between);

if($_SERVER['REQUEST_METHOD']=='POST')
{
	$form->bindAndValidate($_POST['event']);
	if($form->isValid())
	{
		/*
		 * form data is valid, put your code to
		 * register a new user here
		 */
		echo "<h1>Thankyou! You chose the date: " . date('r', $form->time->getCurrentDateTime()) . "</h1>";
	}
}
?>
<form action="/datetime/event.php" method="post">
	<?php echo $form; // this will render the form ?>
</form>