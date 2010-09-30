<?php
require_once 'PHPUnit/Framework.php';
$path = realpath(dirname(__FILE__)) . '/../../lib/form/';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

require_once 'phForm.php';
require_once 'phFormView.php';
require_once 'validator/phValidator.php';
require_once 'validator/phRequiredValidator.php';
require_once 'validator/phStringLengthValidator.php';
require_once 'validator/phCompareValidator.php';
require_once 'validator/phValidatorLogic.php';

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
	
	public function testCompareValidator()
	{
		$password = new phFormDataItem('password');
		$confirmPassword = new phFormDataItem('confirmPassword');
		$confirmPassword->bind('password');
		
		$compareVal = new phCompareValidator($confirmPassword, phCompareValidator::EQUAL, array(
			phCompareValidator::INVALID=>"The passwords are not the same"
		));
		
		$this->assertFalse($compareVal->validate('notequal', $password), 'The validator is correctly not valid');
		$this->assertTrue(in_array('The passwords are not the same', 
							$password->getErrors()), 'error message set properly');
							
		$this->assertTrue($compareVal->validate('password', $password), 'The validator is correctly valid');
		
		$compareVal = new phCompareValidator($confirmPassword, phCompareValidator::NOT_EQUAL);
		$this->assertFalse($compareVal->validate('password', $password), 'The validator is correctly not valid');
		$this->assertTrue($compareVal->validate('notequal', $password), 'The validator is correctly valid');
		
		$confirmPassword->bind(5);
		
		$compareVal = new phCompareValidator($confirmPassword, phCompareValidator::LESS_THAN);
		$this->assertFalse($compareVal->validate(10, $password), 'The validator is correctly not valid');
		$this->assertTrue($compareVal->validate(1, $password), 'The validator is correctly valid');
		
		$compareVal = new phCompareValidator($confirmPassword, phCompareValidator::LESS_EQUAL);
		$this->assertFalse($compareVal->validate(10, $password), 'The validator is correctly not valid');
		$this->assertTrue($compareVal->validate(5, $password), 'The validator is correctly valid');
		
		$compareVal = new phCompareValidator($confirmPassword, phCompareValidator::GREATER_THAN);
		$this->assertFalse($compareVal->validate(1, $password), 'The validator is correctly not valid');
		$this->assertTrue($compareVal->validate(10, $password), 'The validator is correctly valid');
		
		$compareVal = new phCompareValidator($confirmPassword, phCompareValidator::GREATER_EQUAL);
		$this->assertFalse($compareVal->validate(1, $password), 'The validator is correctly not valid');
		$this->assertTrue($compareVal->validate(5, $password), 'The validator is correctly valid');
	}
	
	public function testValidatorLogic()
	{
		$fail = new TestValidatorFail('fail!');
		$pass = new TestValidatorPass();
		$errors = new phTestValidatable();
		
		$logic = new phValidatorLogic($pass);
		$logic->and_($fail);
		$this->assertFalse($logic->validate('test', $errors), 'The and logic correctly fails');
		
		$logic = new phValidatorLogic($pass);
		$logic->or_($fail);
		$this->assertTrue($logic->validate('test', $errors), 'The or logic correctly passes');
		
		$logic = new phValidatorLogic($fail);
		$logic->or_($fail);
		$this->assertFalse($logic->validate('test', $errors), 'The or logic correctly fails');	
		
		$logic = new phValidatorLogic($pass);
		$logic->and_($fail)->or_($pass);
		$this->assertTrue($logic->validate('test', $errors), 'The v and v or v logic correctly passes');
		
		$logic = new phValidatorLogic($fail);
		$logic->and_($fail);
		$errors->resetErrors();
		$logic->validate('test', $errors);
		$this->assertEquals(sizeof($errors->getErrors()), 2, 'The 2 fails messages where added');
		
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

class phTestValidatable implements phValidatable
{
	protected $_errors = array();
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/phValidatable::addError()
	 */
	public function addError($message)
	{
		$this->_errors[] = $message;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/phValidatable::resetErrors()
	 */
	public function resetErrors()
	{
		$this->_errors = array();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/phValidatable::getErrors()
	 */
	public function getErrors()
	{
		return $this->_errors;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/phValidatable::validate()
	 */
	public function validate()
	{
		return sizeof($this->_errors)==0;
	}
}