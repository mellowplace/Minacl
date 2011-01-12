<?php
require_once 'phTestCase.php';
require_once realpath(dirname(__FILE__)) . '/../../lib/form/phLoader.php';
phLoader::registerAutoloader();

class phViewTest extends phTestCase
{
	protected function setUp()
	{
		parent::setup();
		
		$this->template = 'renderTestView';
		$this->form = new phForm('test', $this->template);
		$this->view = new phFormView($this->template, $this->form);
	}
	
    public function testElementFinding()
    {
        $this->assertTrue($this->view->username instanceof phFormDataItem, 'username exists and is a phFormDataItem');
    }
    
    /**
     * @expectedException phFormException
     */
    public function testElementNotfound()
    {
    	$this->view->elementDoesNotExist;
    }
    
	/**
     * @expectedException phFormException
     */
    public function testIdRegisteredButNotInView()
    {
    	$this->view->name('missing');
    	$this->view->missing;
    }
    
	/**
     * @expectedException phFormException
     */
    public function testNameRegisteredButNotInView()
    {
    	$this->view->name('missing');
    	$this->view->missing;
    }
    
    
    /**
     * @expectedException phFormException
     */
    public function testNoFactory()
    {
    	$template = 'invalidElementView';
    	$view = new phFormView($template, new phForm('test', $template));
    	$view->invalid;
    }
    
    /**
     * @expectedException phFormException
     */
    public function testRewrittenNameNotFound()
    {
    	$this->view->getRewrittenName('nonexistantname');
    }
    
	/**
     * @expectedException phFormException
     */
    public function testRealIdNotFound()
    {
    	$this->view->getRealId('nonexistantid');
    }
    
	/**
     * @expectedException phFormException
     */
    public function testElementNameButNoId()
    {
    	$template = 'invalidElementView';
    	$view = new phFormView($template, new phForm('test', $template));
    	$view->noId;
    }
    
    /**
     * @expectedException phFormException
     */
    public function testInvalidElementId()
    {
    	$this->view->id('123nostartwithnumbers');
    }
    
	/**
     * @expectedException phFormException
     */
    public function testInvalidElementId2()
    {
    	$this->view->id('no spaces');
    }
    
	/**
     * @expectedException phFormException
     */
    public function testInvalidElementId3()
    {
    	$this->view->id('$pecialch£rsnotallowed');
    }
    
	/**
     * @expectedException phFormException
     */
    public function testInvalidElementId4()
    {
    	$this->view->id('_noUnderscoreStart');
    }
    
    public function testValidElementId()
    {
    	$this->assertEquals($this->view->id('idIsGood'), 'test_idIsGood', 'element id set ok');
    }
    
	public function testValidElementId2()
    {
    	$this->assertEquals($this->view->id('underscores_are_ok'), 'test_underscores_are_ok', 'element id set ok');
    }
    
    public function testValidNames()
    {
    	$this->assertEquals($this->view->name('goodName'), 'test[goodName]');
    	$this->assertEquals($this->view->name('nameWithNumbers123'), 'test[nameWithNumbers123]');
    	$this->assertEquals($this->view->name('arrayData[]'), 'test[arrayData][]');
    	$this->assertEquals($this->view->name('associativeArray[key]'), 'test[associativeArray][key]');
    	$this->assertEquals($this->view->name('numericKey[1]'), 'test[numericKey][1]');
    	$this->assertEquals($this->view->name('multiDimensional[1][1]'), 'test[multiDimensional][1][1]');
    }
    
    /**
     * @expectedException phFormException
     */
    public function testInvalidNameWithSpaces()
    {
    	$this->view->name('Bad Name');
    }
    
	/**
     * @expectedException phFormException
     */
    public function testInvalidName2()
    {
    	$this->view->name('BadName[');
    }
    
	/**
     * @expectedException phFormException
     */
    public function testInvalidName3()
    {
    	$this->view->name('BadN]a[me');
    }
    
	/**
     * @expectedException phFormException
     */
    public function testInvalidName4()
    {
    	$this->view->name('123BadName'); // can't start with numbers
    }
    
	/**
     * @expectedException phFormException
     */
    public function testInvalidName5()
    {
    	$this->view->name('_BadName'); // can't start with _
    }
    
	/**
     * @expectedException phFormException
     */
    public function testInvalidName6()
    {
    	$this->view->name('BadName[]moo'); // can't have anything after the array close
    }
    
	/**
     * @expectedException phFormException
     */
    public function testInvalidName7()
    {
    	$this->view->name('BadName[[]'); // bad character in the array key
    }
    
	/**
     * @expectedException phFormException
     */
    public function testInvalidName8()
    {
    	$this->view->name('BadName[@]'); // bad character in the array key
    }
    
	/**
     * @expectedException phFormException
     */
    public function testInvalidName9()
    {
    	$this->view->name('BadName]'); // unopened array
    }
    
	/**
     * @expectedException phFormException
     */
    public function testInvalidName10()
    {
    	$this->view->name('BadName['); // unclosed array
    }
    
	/**
     * @expectedException phFormException
     */
    public function testInvalidName11()
    {
    	$this->view->name('badName');
    	$this->view->name('badName[]'); // badName is already registered
    }
    
    public function testRender()
    {
    	$html = $this->view->render();
    	
    	$template = 'renderTestView';
		$view = new phFormViewTest($template, new phForm('test', $template));
		$this->assertEquals($html, $view->render(), 'Rendered HTML is same as original');
    }
    
    public function testSetValue()
    {
    	$this->view->username->bind("Test123 ABC");
    	$html = $this->view->render();
    	
    	$this->assertContains('"Test123 ABC"', $html, 'The username value was set and rendered correctly');
    }
    
	public function testMagicGetReturnsSameInstance()
    {
    	$username1 = $this->view->username;
    	$username2 = $this->view->username;
    	
    	$this->assertSame($username1, $username2, '__get returns a reference and isn\'t creating a new object each time');
    }
    
    /**
     * @expectedException phFormException
     */
    public function testCrapHtml()
    {
    	$template = 'crapHtmlView';
    	new phFormView($template, new phForm('test', $template));
    }
    
    public function testErrorListing()
    {
    	$username = $this->view->username;
    	$username->setValidator(new phRequiredValidator(array(phRequiredValidator::REQUIRED=>'Field is required')));
    	$username->bind('');
    	$username->validate();
    	
    	$errorList = $this->view->errorList('username');
    	$this->assertTrue(strstr($errorList, 'Field is required')!==false, 'The required error was listed in the view');
    }
    
    public function testGetElement()
    {
    	$username = $this->view->getElement('username');
    	$this->assertTrue($username instanceof phInputElement, 'username is a phInputElement');
    	
    	$rewrittenId = $this->view->id('username');
    	$this->assertEquals((string)$username->getElement()->attributes()->id, $rewrittenId, 'getElement returned the correct element');
    }
    
    /**
     * @expectedException phFormException
     */
	public function testGetElementWithNonExistantElement()
    {
    	$username = $this->view->getElement('nonExistantId');
    }
    
    public function testArrays()
    {
    	$view = new phFormView('arrayTestView', new phForm('test', 'arrayTestView'));
    	$this->assertTrue($view->ids instanceof phArrayFormDataItem, 'The ids data is accessible and is an instance of phArrayFormDataItem');
    	$this->assertEquals(sizeof($view->ids), 5, 'There are 5 elements in ids');
    }
}

class phFormViewTest extends phFormView
{
	public function getDom()
	{
		return parent::getDom();
	}
}
?>
