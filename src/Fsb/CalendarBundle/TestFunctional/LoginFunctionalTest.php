<?php
namespace Fsb\AppointmentBundle\TestFunctional;

use Symfony\Component\Validator\Validation;
use Fsb\CalendarBundle\TestFunctional\DefaultFunctionalTest;
use Fsb\CalendarBundle\TestFunctional\Util\LoginFunctionalTestUtil;
use Fsb\CalendarBundle\TestFunctional\Util\AppointmentFunctionalTestUtil;

class LoginFunctionalTest extends DefaultFunctionalTest {
	
	
	public function testHome()
	{
		$this->webDriver->get($this->url);
		// checking that page title contains word 'GitHub'
		$this->assertContains('Fsb Calendar', $this->webDriver->getTitle());
	}
	
	public function testLogin()
	{
		$this->webDriver->get($this->url);
		
		LoginFunctionalTestUtil::login($this->webDriver);
		
		// checking current url
		$currentDate = new \DateTime('now');
		$this->assertEquals(
				$this->url.'/calendar/month/'.$currentDate->format('m').'/year/'.$currentDate->format('Y'),
				$this->webDriver->getCurrentURL()
		);
	}
	
}