<?php
namespace Fsb\CalendarBundle\TestFunctional\Util;


class AppointmentFunctionalTestUtil {
	
	static public function newAppointment(\RemoteWebDriver $webDriver) {
		
		$webDriver->wait()->until(\WebDriverExpectedCondition::elementToBeClickable(\WebDriverBy::id('fsb_appointmentbundle_appointment_recruiter')));
		$webDriver->findElement(\WebDriverBy::id('fsb_appointmentbundle_appointment_recruiter'))->click();
		$webDriver->getKeyboard()->pressKey(\WebDriverKeys::ARROW_DOWN);
		$webDriver->getKeyboard()->pressKey(\WebDriverKeys::ENTER);
	
	
		$webDriver->findElement(\WebDriverBy::id("fsb_appointmentbundle_appointment_appointmentDetail_title"))->click();
		$webDriver->getKeyboard()->sendKeys('Title 1');
	
		$webDriver->findElement(\WebDriverBy::id('fsb_appointmentbundle_appointment_appointmentDetail_project'))->click();
		$webDriver->getKeyboard()->pressKey(\WebDriverKeys::ARROW_DOWN);
		$webDriver->getKeyboard()->pressKey(\WebDriverKeys::ENTER);
	
		$webDriver->findElement(\WebDriverBy::id("fsb_appointmentbundle_appointment_appointmentDetail_recordRef"))->click();
		$webDriver->getKeyboard()->sendKeys('Record Reference 1');
	
		$webDriver->findElement(\WebDriverBy::id('fsb_appointmentbundle_appointment_appointmentDetail_outcome'))->click();
		$webDriver->getKeyboard()->pressKey(\WebDriverKeys::ARROW_DOWN);
		$webDriver->getKeyboard()->pressKey(\WebDriverKeys::ENTER);
	
		$webDriver->findElement(\WebDriverBy::id("fsb_appointmentbundle_appointment_appointmentDetail_outcomeReason"))->click();
		$webDriver->getKeyboard()->sendKeys('Outcome Reason 1');
	
		$webDriver->findElement(\WebDriverBy::id("fsb_appointmentbundle_appointment_appointmentDetail_address_add1"))->click();
		$webDriver->getKeyboard()->sendKeys('Add 1');
	
		$webDriver->findElement(\WebDriverBy::id("fsb_appointmentbundle_appointment_appointmentDetail_address_add2"))->click();
		$webDriver->getKeyboard()->sendKeys('Add 2');
	
		$webDriver->findElement(\WebDriverBy::id("fsb_appointmentbundle_appointment_appointmentDetail_address_add3"))->click();
		$webDriver->getKeyboard()->sendKeys('Add 3');
	
		$webDriver->findElement(\WebDriverBy::id("fsb_appointmentbundle_appointment_appointmentDetail_address_postcode"))->click();
		$webDriver->getKeyboard()->sendKeys('M15 4JR');
	
		$webDriver->findElement(\WebDriverBy::id("fsb_appointmentbundle_appointment_appointmentDetail_address_town"))->click();
		$webDriver->getKeyboard()->sendKeys('Town 1');
	
		$webDriver->findElement(\WebDriverBy::id("fsb_appointmentbundle_appointment_appointmentDetail_address_country"))->click();
		$webDriver->getKeyboard()->sendKeys('UK');
	
		$webDriver->findElement(\WebDriverBy::id("fsb_appointmentbundle_appointment_appointmentDetail_comment"))->click();
		$webDriver->getKeyboard()->sendKeys('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.');
	
		$webDriver->findElement(\WebDriverBy::id('fsb_appointmentbundle_appointment_submit'))->click();
	
	}
	
	static public function editAppointment(\RemoteWebDriver $webDriver) {
	
		$webDriver->wait()->until(\WebDriverExpectedCondition::elementToBeClickable(\WebDriverBy::id('fsb_appointmentbundle_appointmentedit_recruiter')));
	
		$webDriver->findElement(\WebDriverBy::id('fsb_appointmentbundle_appointmentedit_recruiter'))->click();
		$webDriver->getKeyboard()->pressKey(\WebDriverKeys::ARROW_DOWN);
		$webDriver->getKeyboard()->pressKey(\WebDriverKeys::ENTER);
		
		$webDriver->findElement(\WebDriverBy::id('fsb_appointmentbundle_appointmentedit_appointmentSetter'))->click();
		$webDriver->getKeyboard()->pressKey(\WebDriverKeys::ARROW_DOWN);
		$webDriver->getKeyboard()->pressKey(\WebDriverKeys::ENTER);
	
		$webDriver->findElement(\WebDriverBy::id('fsb_appointmentbundle_appointmentedit_submit'))->click();
	}
	
}