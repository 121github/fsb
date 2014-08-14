<?php
namespace Fsb\CalendarBundle\TestFunctional;

use Symfony\Component\Validator\Validation;
use Fsb\CalendarBundle\TestFunctional\DefaultFunctionalTest;
use Fsb\CalendarBundle\TestFunctional\Util\LoginFunctionalTestUtil;
use Fsb\CalendarBundle\TestFunctional\Util\AppointmentFunctionalTestUtil;

class AppointmentFunctionalTest extends DefaultFunctionalTest {
	
	
	private $startTime = "16:00";
	private $currentDate;
	private $dayViewUrl;
	
	public function setUp() {
		parent::setUp();
		
		$this->currentDate  = new \DateTime('today '.$this->startTime);
		$this->dayViewUrl = $this->url.'/calendar/day/'.$this->currentDate->format('d').'/month/'.$this->currentDate->format('m').'/year/'.$this->currentDate->format('Y');
	}
	
	public function testCreateAppointmentFromDayView()
	{
		$this->webDriver->get($this->dayViewUrl);
		
		//Login
		LoginFunctionalTestUtil::login($this->webDriver);
		
		//Wait after login action until the page is loaded
		$this->webDriver->wait()->until(\WebDriverExpectedCondition::titleIs('Calendar | Fsb Calendar Management System'));
		
		//Click on the link where we want to create the appointment
		$this->webDriver->findElement(\WebDriverBy::linkText($this->startTime))->click();
		
		//Click on Create Appointment into the popUp
		$this->webDriver->findElement(\WebDriverBy::linkText('Create Appointment'))->click();
		
		//Create appointment: fill the form and submit
		AppointmentFunctionalTestUtil::newAppointment($this->webDriver);
		
		//Wait after submit action until the page is loaded
		$this->webDriver->wait()->until(\WebDriverExpectedCondition::titleIs('Calendar | Fsb Calendar Management System'));
		
		//Check if the appointment has been created
		$this->assertContains('Appointment Created!', $this->webDriver->findElement(\WebDriverBy::id('message'))->getText());
		
	}
	
	public function testEditAppointmentFromDayView()
	{
		$this->webDriver->get($this->dayViewUrl);
	
		//Login
		LoginFunctionalTestUtil::login($this->webDriver);
	
		//Wait after login action until the page is loaded
		$this->webDriver->wait()->until(\WebDriverExpectedCondition::titleIs('Calendar | Fsb Calendar Management System'));
	
		//Click on the link where we want to create the appointment
		$this->webDriver->findElement(\WebDriverBy::id($this->startTime.' - Title 1'))->click();
	
		//Click on Create Appointment into the popUp
		$this->webDriver->findElement(\WebDriverBy::linkText('Edit'))->click();
	
		//Create appointment: fill the form and submit
		AppointmentFunctionalTestUtil::editAppointment($this->webDriver);

	
		//Wait after submit action until the page is loaded
		$this->webDriver->wait()->until(\WebDriverExpectedCondition::titleIs('Calendar | Fsb Calendar Management System'));
	
		//Check if the appointment has been created
		$this->assertContains('Appointment Updated!', $this->webDriver->findElement(\WebDriverBy::id('message'))->getText());
	
	}
	
	
	
}