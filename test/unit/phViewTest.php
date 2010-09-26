<?php
require_once 'PHPUnit/Framework.php';
$path = realpath(dirname(__FILE__)) . '/../../lib/form/';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

require_once 'phForm.php';
require_once 'phFormView.php';

class phViewTest extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		$this->template = realpath(dirname(__FILE__)) . '/../resources/renderTestView.php';
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
    	$template = realpath(dirname(__FILE__)) . '/../resources/invalidElementView.php';
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
    	$template = realpath(dirname(__FILE__)) . '/../resources/invalidElementView.php';
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
    
    public function testRender()
    {
    	$html = $this->view->render();
    	
    	$template = realpath(dirname(__FILE__)) . '/../resources/renderTestView.php';
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
    	$template = dirname(__FILE__) . '/../resources/crapHtmlView.php';
    	new phFormView($template, new phForm('test', $template));
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
