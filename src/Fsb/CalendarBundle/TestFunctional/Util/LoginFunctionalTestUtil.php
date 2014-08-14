<?php
namespace Fsb\CalendarBundle\TestFunctional\Util;


class LoginFunctionalTestUtil {
	
	static public function login(\RemoteWebDriver $webDriver) {
	
		// find username field by its id
		$search = $webDriver->findElement(\WebDriverBy::id('username'));
		$search->click();
		// typing into field
		$webDriver->getKeyboard()->sendKeys('User9');
	
		// find password field by its id
		$search = $webDriver->findElement(\WebDriverBy::id('password'));
		$search->click();
		// typing into field
		$webDriver->getKeyboard()->sendKeys('User9');
	
		// pressing "Enter"
		$webDriver->getKeyboard()->pressKey(\WebDriverKeys::ENTER);
	}
	
}