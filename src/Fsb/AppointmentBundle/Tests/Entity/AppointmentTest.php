<?php

use Symfony\Component\Validator\Validation;
use Fsb\AppointmentBundle\Entity\Appointment;
use Fsb\UserBundle\Entity\User;
use Fsb\UserBundle\Entity\UserRole;

class AppointmentTest extends \PHPUnit_Framework_TestCase
{
	private $validator;
	private $recruiter;
	
	
	public function setUp()
	{
		$this->validator = Validation::createValidatorBuilder()
		->enableAnnotationMapping()
		->getValidator();
		
		
		$recruiter = new User();
		$recruiter->setLogin('login');
		$this->recruiter = $recruiter;
	}
	
	public function testValidacion()
	{
		$appointment = new Appointment();
		
		$appointment->setRecruiter($this->recruiter);
		$this->assertEquals(
				'login',
				$appointment->getRecruiter()->getLogin(),
				'The recruiter is saved in the appointment'
		);
		
		
	}
}