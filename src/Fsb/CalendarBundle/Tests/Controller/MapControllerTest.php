<?php

namespace Fsb\AppointmentBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MapControllerTest extends WebTestCase
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
	public function testMapDayAction()
	{
		$url = $this->generateUrl('calendar_homepage', array());
		
		$crawler = $this->client->request('GET', $url);
		
		$appointmentLink = $crawler->selectLink('Day')->link();
		$this->client->click($appointmentLink);
		
		$appointmentLink = $crawler->selectLink('Map')->link();
		$this->client->click($appointmentLink);
		
		$this->assertTrue(200 === $this->client->getResponse()->getStatusCode());
	}
	
	/**
	 *
	 */
	public function testMapMonthAction()
	{
		$url = $this->generateUrl('calendar_homepage', array());
	
		$crawler = $this->client->request('GET', $url);
	
		$appointmentLink = $crawler->selectLink('Month')->link();
		$this->client->click($appointmentLink);
	
		$appointmentLink = $crawler->selectLink('Map')->link();
		$this->client->click($appointmentLink);
	
		$this->assertTrue(200 === $this->client->getResponse()->getStatusCode());
	}
	
	/**
	 *
	 */
	public function testMapDiaryAction()
	{
		$url = $this->generateUrl('calendar_homepage', array());
	
		$crawler = $this->client->request('GET', $url);
	
		$appointmentLink = $crawler->selectLink('Diary')->link();
		$this->client->click($appointmentLink);
	
		$appointmentLink = $crawler->selectLink('Map')->link();
		$this->client->click($appointmentLink);
	
		$this->assertTrue(200 === $this->client->getResponse()->getStatusCode());
	}

}