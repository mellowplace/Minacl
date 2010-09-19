<?php
require_once 'PHPUnit/Framework.php';
$path = realpath(dirname(__FILE__)) . '/../../lib/form/';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

require_once 'phForm.php';
require_once 'phFormView.php';
require_once 'validator/phValidator.php';
require_once 'validator/phRequiredValidator.php';

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
	
	public function testElementErrorMessages()
	{
		$this->form->username->setValidator(new TestValidatorFail('This field is required'));
		$this->form->bind(array(
			'username'=>'fail',
			'password'=>'fail'
		));
		
		$this->assertTrue(in_array('This field is required', $this->form->username->getErrors()), 
			'The error message is returned in form->username->getErrors');
	}
	
	public function testFormErrorMessages()
	{
		$this->form->username->setValidator(new TestValidatorFail('This field is required'));
		$this->form->bind(array(
			'username'=>'fail',
			'password'=>'fail'
		));
		
		$this->assertTrue(in_array('This field is required', $this->form->getErrors()), 
			'The error message is returned in form->getErrors');
	}
	
	public function testViewErrorMessages()
	{
		$this->form->username->setValidator(new TestValidatorFail('This field is required'));
		$this->form->bind(array(
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
		$this->form->bind(array(
			'username'=>'pass',
			'password'=>'pass'
		));
		$this->assertTrue($this->form->isValid(), 'Form is correctly valid');
	}
	
	public function testRequiredValidatorFail()
	{
		$this->form->username->setValidator(new phRequiredValidator("Username is required"));
		$this->form->bind(array(
			'username'=>'',
			'password'=>'fail'
		));
		
		$this->assertFalse($this->form->username->isValid(), 'The username is correctly invalid');
		$this->assertTrue(in_array('Username is required', $this->form->username->getErrors()),
			'The username required validator error was set properly');
	}
	
	public function testRequiredValidatorPass()
	{
		$this->form->username->setValidator(new phRequiredValidator("Username is required"));
		$this->form->bind(array(
			'username'=>'here',
			'password'=>'fail'
		));
		
		$this->assertTrue($this->form->username->isValid(), 'The username is valid');
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
	
	public function validate(phElement $checkElement, phForm $bindingForm)
	{
		$checkElement->addError($this->_message);
		return false;
	}
}

class TestValidatorPass implements phValidator
{
	public function validate(phElement $checkElement, phForm $bindingForm)
	{
		return true;
	}
}