<?php

namespace Fsb\AppointmentBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AppointmentControllerTest extends WebTestCase
{
	private $client;
	private $eManager;
	
	public function setUp() {
		$this->client = static::createClient(array(), array(
				'PHP_AUTH_USER' => 'User9',
				'PHP_AUTH_PW'   => 'User9',
		));
	
		$this->client->followRedirects(true);
	
		$this->eManager = static::$kernel->getContainer()->get('doctrine')->getManager();
	}
	
	private function generateUrl($routingName, $args) {
		$url = $this->client->getContainer()->get('router')->generate($routingName,$args);
	
		return $url;
	}
	
	/**
	 * 
	 */
	public function testDayAction()
	{
		$date = new \DateTime('now');
		$url = $this->generateUrl('calendar_day', array(
				'day' => $date->format('d'),
				'month' => $date->format('m'),
				'year' => $date->format('Y'),
		));
		
		$crawler = $this->client->request('GET', $url);

		$this->assertTrue(200 === $this->client->getResponse()->getStatusCode());
	}
	
	/**
	 *
	 */
	public function testDayActionByRecruiter()
	{
		$recruiterList = $this->eManager->getRepository('UserBundle:User')->findUsersByRole('ROLE_RECRUITER');
		$recruiter = $recruiterList[0];
		
		$date = new \DateTime('now');
		$url = $this->generateUrl('calendar_day', array(
				'day' => $date->format('d'),
				'month' => $date->format('m'),
				'year' => $date->format('Y'),
				'recruiter_id' => $recruiter->getId(),
		));
	
		$crawler = $this->client->request('GET', $url);
	
		$this->assertTrue(200 === $this->client->getResponse()->getStatusCode());
	}

}