<?php
namespace Fsb\CalendarBundle\TestFunctional;

use Symfony\Component\Validator\Validation;

abstract class DefaultFunctionalTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * @var \RemoteWebDriver
	 */
	protected $webDriver;
	protected $url = 'http://localhost/projects/fsb/web';
	
	protected function setUp()
	{
		//$capabilities = array(\WebDriverCapabilityType::BROWSER_NAME => 'htmlunit');
		$capabilities = array(\WebDriverCapabilityType::BROWSER_NAME => 'firefox');
		//$capabilities = array(\WebDriverCapabilityType::BROWSER_NAME => 'chrome');
		//$capabilities = array(\WebDriverCapabilityType::BROWSER_NAME => 'internet explorer');
		$this->webDriver = \RemoteWebDriver::create('http://localhost:4444/wd/hub', $capabilities);
	}
	
	protected function tearDown()
	{
		$this->webDriver->close();
	}
	
	
	protected function waitForUserInput()
	{
		if(trim(fgets(fopen("php://stdin","r"))) != chr(13)) return;
	}
	
	protected function assertElementNotFound($by)
	{
		$els = $this->webDriver->findElements($by);
		if (count($els)) {
			$this->fail("Unexpectedly element was found");
		}
		// increment assertion counter
		$this->assertTrue(true);
	}
}