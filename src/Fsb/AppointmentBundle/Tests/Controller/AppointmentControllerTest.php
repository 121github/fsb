<?php

namespace Fsb\AppointmentBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;

class AppointmentControllerTest extends WebTestCase
{
	private $client;
	
	public function setUp() {
		$this->client = static::createClient(array(), array(
				'PHP_AUTH_USER' => 'User9',
				'PHP_AUTH_PW'   => 'User9',
		));
	}
	
// 	public function testDayViewAction()
// 	{
// 		$date = new \DateTime('now');
// 		$crawler = $this->client->request('GET', '/calendar/day/'.$date->format('d').'/month/'.$date->format('m').'/year/'.$date->format('Y'));
		
// 		$this->assertTrue(200 === $this->client->getResponse()->getStatusCode());
// 	}
	
	public function testNewDateAction()
	{
		
	
		$date = new \DateTime('now');
		$crawler = $this->client->request('GET', '/calendar/appointment/new/hour/'.$date->format('H').'/minute/'.$date->format('i').'/day/'.$date->format('d').'/month/'.$date->format('m').'/year/'.$date->format('Y'));
	
		$this->assertTrue(200 === $this->client->getResponse()->getStatusCode());
	}
}
