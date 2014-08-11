<?php

use Symfony\Component\Validator\Validation;
use Fsb\AppointmentBundle\Entity\Appointment;
use Fsb\UserBundle\Entity\User;
use Fsb\UserBundle\Entity\UserRole;
use Fsb\AppointmentBundle\Entity\AppointmentDetail;

class AppointmentDetailTest extends \PHPUnit_Framework_TestCase
{
	private $validator;
	private $recruiter;
	private $appointmentSetter;
	private $appointmentDetail;
	
	
	public function setUp()
	{
		$this->validator = Validation::createValidatorBuilder()
		->enableAnnotationMapping()
		->getValidator();
		
		
		$recruiter = new User();
		$recruiter->setLogin('login');
		$this->recruiter = $recruiter;
		
		$appointmentSetter = new User();
		$appointmentSetter->setLogin('login');
		$this->appointmentSetter = $appointmentSetter;
		
		$appointmentDetail = new AppointmentDetail();
		$appointmentDetail->setTitle('title');
		$this->appointmentDetail = $appointmentDetail;
	}
	
	public function testValidation()
	{
		$appointment = new Appointment();
		
		$appointment->setRecruiter($this->recruiter);
		$this->assertEquals(
				'login',
				$appointment->getRecruiter()->getLogin(),
				'The recruiter is saved in the appointment'
		);
		
		$appointment->setAppointmentSetter($this->appointmentSetter);
		$this->assertEquals(
				'login',
				$appointment->getAppointmentSetter()->getLogin(),
				'The appointmentSetter is saved in the appointment'
		);
		
		$appointment->setAppointmentDetail($this->appointmentDetail);
		$this->assertEquals(
				'title',
				$appointment->getAppointmentDetail()->getTitle(),
				'The appointmentDetail is saved in the appointment'
		);
		
		$appointmentDetail = $appointment->getAppointmentDetail();
		$appointmentDetail->setAppointment($appointment);
		$this->assertEquals(
				$appointment->getAppointmentDetail()->getTitle(),
				$appointmentDetail->getAppointment()->getAppointmentDetail()->getTitle(),
				'The appointmentDetail setted in the appointment is the same than the appointmentDetail getted from the appointment stored in the apppointmentDetail'	
		);
	}
}