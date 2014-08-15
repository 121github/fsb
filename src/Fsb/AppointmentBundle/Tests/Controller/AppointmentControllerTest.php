<?php

namespace Fsb\AppointmentBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Fsb\UserBundle\Entity\User;

class AppointmentControllerTest extends WebTestCase
{
	private $client;
	private $eManager;
	private $currentDate;
	private $startTime;
	private $endTime;
	
	public function setUp() {
		$this->client = static::createClient(array(), array(
				'PHP_AUTH_USER' => 'User9',
				'PHP_AUTH_PW'   => 'User9',
		));
		
		$this->client->followRedirects(true);
		
		$this->eManager = static::$kernel->getContainer()->get('doctrine')->getManager();
		
		$this->currentDate = new \DateTime('now');
		$this->startTime = new \DateTime($this->currentDate->format('Y-m-d').' 08:00:00');
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
		$url = $this->generateUrl('appointment_new_date', array(
				'hour' => $this->currentDate->format('H'),
				'minute' => $this->currentDate->format('i'),
				'day' => $this->currentDate->format('d'),
				'month' => $this->currentDate->format('m'),
				'year' => $this->currentDate->format('Y'),
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
	
		$url = $this->generateUrl('appointment_new_date', array(
				'hour' => $this->currentDate->format('H'),
				'minute' => $this->currentDate->format('i'),
				'day' => $this->currentDate->format('d'),
				'month' => $this->currentDate->format('m'),
				'year' => $this->currentDate->format('Y'),
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
	
	/**
	 *
	 */
	public function testOutcomeEditAction() {
		$appointmentList = $this->eManager->getRepository('AppointmentBundle:Appointment')->findAll();
		$appointment = $appointmentList[0];
		
		$url = $this->generateUrl('appointment_outcome_edit', array(
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
	
	/**
	 *
	 */
	public function testSearchAppointmentAction() {
	
		$url = $this->generateUrl('appointment_filter', array(
				'month' => $this->currentDate->format('m'),
				'year' => $this->currentDate->format('Y'),
		));
	
		$crawler = $this->client->request('GET', $url);
	
		//Status code 200
		$this->assertTrue(200 === $this->client->getResponse()->getStatusCode());
	}
}
