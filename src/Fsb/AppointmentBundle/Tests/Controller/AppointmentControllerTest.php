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
	public function testNewDateAction()
	{

		$date = new \DateTime('now');
		$url = $this->generateUrl('appointment_new_date', array(
				'hour' => $date->format('H'),
				'minute' => $date->format('i'),
				'day' => $date->format('d'),
				'month' => $date->format('m'),
				'year' => $date->format('Y'),
		));
		
		$crawler = $this->client->request('GET', $url);
	
		//Status code 200
		$this->assertTrue(200 === $this->client->getResponse()->getStatusCode());
		
		//Contains the create button
		$this->assertEquals(1, $crawler->filter('html:contains("Create")')->count(),
				'The new appointment page has a Create submit link'
		);
	}
	
	/**
	 * 
	 */
	public function testNewDateActionByRecruiter()
	{
		$recruiterList = $this->eManager->getRepository('UserBundle:User')->findUsersByRole('ROLE_RECRUITER');
		$recruiter = $recruiterList[0];
	
		$date = new \DateTime('now');
		$url = $this->generateUrl('appointment_new_date', array(
				'hour' => $date->format('H'),
				'minute' => $date->format('i'),
				'day' => $date->format('d'),
				'month' => $date->format('m'),
				'year' => $date->format('Y'),
				'recruiter_id' => $recruiter->getId(),
		));
	
		$crawler = $this->client->request('GET', $url);
		
		//Status code 200
		$this->assertTrue(200 === $this->client->getResponse()->getStatusCode());
	
		//Contains the create button
		$this->assertEquals(1, $crawler->filter('html:contains("Create")')->count(),
				'The new appointment page has a Create submit link'
		);
	}
	
	/**
	 * 
	 */
	public function testShowAction() {
		$appointmentList = $this->eManager->getRepository('AppointmentBundle:Appointment')->findAll();
		$appointment = $appointmentList[0];
	
		$url = $this->generateUrl('appointment_show', array(
				'appointmentId' => $appointment->getId(),
		));
	
		$crawler = $this->client->request('GET', $url);
	
		//Status code 200
		$this->assertTrue(200 === $this->client->getResponse()->getStatusCode());
	
		//Contains the edit button
		$this->assertEquals(1, $crawler->filter('html:contains("Edit")')->count(),
				'The show appointment page has a Edit submit link'
		);
	}
	
	/**
	 * 
	 */
	public function testEditAction() {
		$appointmentList = $this->eManager->getRepository('AppointmentBundle:Appointment')->findAll();
		$appointment = $appointmentList[0];
	
		$url = $this->generateUrl('appointment_edit', array(
				'appointmentId' => $appointment->getId(),
		));
	
		$crawler = $this->client->request('GET', $url);
	
		//Status code 200
		$this->assertTrue(200 === $this->client->getResponse()->getStatusCode());
	
		//Contains the Update button
		$this->assertEquals(1, $crawler->filter('html:contains("Update")')->count(),
				'The edit appointment page has a Update submit link'
		);
	}
}
