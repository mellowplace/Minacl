<?php
require_once 'PHPUnit/Framework.php';
$path = realpath(dirname(__FILE__)) . '/../../lib/form/';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

require_once 'phForm.php';
require_once 'phFormView.php';
require_once 'validator/phValidator.php';
require_once 'validator/phRequiredValidator.php';
require_once 'validator/phStringLengthValidator.php';

class phValidatorTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->form = new TestForm('test', dirname(__FILE__) . '/../resources/simpleTestView.php');
	}
	
	public function testFormFail()
	{
		$this->form->username->setValidator(new TestValidatorFail('required'));
		$this->form->bind(array(
			'username'=>'fail',
			'password'=>'fail'
		));
		$this->assertFalse($this->form->isValid(), 'Form is correctly not valid');
	}
	
	public function testEmptyPostFormFail()
	{
		$this->form->username->setValidator(new TestValidatorFail('required'));
		$this->form->username->bind('');
		$this->form->bind(array());
		$this->assertFalse($this->form->isValid(), 'Form is correctly not valid');
	}
	
	public function testElementErrorMessages()
	{
		$this->form->username->setValidator(new TestValidatorFail('This field is required'));
		$this->form->bindAndValidate(array(
			'username'=>'fail',
			'password'=>'fail'
		));
		
		$this->assertTrue(in_array('This field is required', $this->form->username->getErrors()), 
			'The error message is returned in form->username->getErrors');
	}
	
	public function testFormErrorMessages()
	{
		$this->form->username->setValidator(new TestValidatorFail('This field is required'));
		$this->form->bindAndValidate(array(
			'username'=>'fail',
			'password'=>'fail'
		));
		
		$this->assertTrue(in_array('This field is required', $this->form->getErrors()), 
			'The error message is returned in form->getErrors');
	}
	
	public function testViewErrorMessages()
	{
		$this->form->username->setValidator(new TestValidatorFail('This field is required'));
		$this->form->bindAndValidate(array(
			'username'=>'fail',
			'password'=>'fail'
		));
		
		$view = $this->form->getView();
		$this->assertTrue(in_array('This field is required', $view->error('username')), 
			'The error message is returned in view->error(\'username\')');
	}
	
	public function testFormPass()
	{
		$this->form->username->setValidator(new TestValidatorPass());
		$this->form->bindAndValidate(array(
			'username'=>'pass',
			'password'=>'pass'
		));
		$this->assertTrue($this->form->isValid(), 'Form is correctly valid');
	}
	
	public function testRequiredValidatorFail()
	{
		$this->form->username->setValidator(new phRequiredValidator(
			array(phRequiredValidator::REQUIRED=>'Username is required')
		));
		$this->form->bind(array(
			'username'=>'',
			'password'=>'fail'
		));
		
		$this->assertFalse($this->form->username->validate(), 'The username is correctly invalid');
		$this->assertTrue(in_array('Username is required', $this->form->username->getErrors()),
			'The username required validator error was set properly');
	}
	
	public function testRequiredValidatorPass()
	{
		$this->form->username->setValidator(new phRequiredValidator(array(
			phRequiredValidator::REQUIRED=>"Username is required"
		)));
		$this->form->bind(array(
			'username'=>'here',
			'password'=>'fail'
		));
		
		$this->assertTrue($this->form->username->validate(), 'The username is valid');
	}
	
	public function testStringLengthValidator()
	{
		$strVal = new phStringLengthValidator(array(
			phStringLengthValidator::INVALID=>"Please enter a string between 6 and 8 characters long"
		));
		$strVal->min(6)->max(8);
		
		$username = new phFormDataItem('username');
		$username->setValidator($strVal);
		
		$username->bind('short');
		$this->assertFalse($strVal->validate($username->getValue(), $username), 'The validator is correctly not valid');
		$this->assertTrue(in_array('Please enter a string between 6 and 8 characters long', 
							$username->getErrors()), 'error message set properly');
		
		$username->bind('tooloooooooong');
		$this->assertFalse($strVal->validate($username->getValue(), $username), 'The validator is correctly not valid');
		
		$username->bind('justfine');
		$this->assertTrue($strVal->validate($username->getValue(), $username), 'The validator is valid');
		
		$strVal->min(6)->max(null);
		$username->bind('short');
		$this->assertFalse($strVal->validate($username->getValue(), $username), 'The validator is correctly not valid');
		
		$username->bind('123456');
		$this->assertTrue($strVal->validate($username->getValue(), $username), 'The validator is correctly valid');
		
		$strVal->min(null)->max(10);
		$username->bind('');
		$this->assertTrue($strVal->validate($username->getValue(), $username), 'The validator is correctly valid');
		
		$username->bind('1234567890');
		$this->assertTrue($strVal->validate($username->getValue(), $username), 'The validator is correctly valid');
		
		$username->bind('waaaaaaaayyyytooooolong');
		$this->assertFalse($strVal->validate($username->getValue(), $username), 'The validator is correctly not valid');
	}
	
	public function testUnboundFormFail()
	{
		$this->assertFalse($this->form->isValid(), 'Unbound form is not valid');
	}
}

class TestForm extends phForm
{
	/**
	 * @return phFormView
	 */
	public function getView()
	{
		return $this->_view;
	}
}

class TestValidatorFail implements phValidator
{
	public function __construct($message)
	{
		$this->_message = $message;
	}
	
	public function validate($value, phValidatable $item)
	{
		$item->addError($this->_message);
		return false;
	}
}

class TestValidatorPass implements phValidator
{
	public function validate($value, phValidatable $item)
	{
		return true;
	}
}