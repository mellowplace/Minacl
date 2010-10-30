<?php
require_once 'PHPUnit/Framework.php';
class phTestCase extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		phViewLoader::setInstance(
			new phFileViewLoader(realpath(dirname(__FILE__) . '/../resources'))
		);
	}
}