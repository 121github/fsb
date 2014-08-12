<?php

use Symfony\Component\Validator\Validation;
use Fsb\AppointmentBundle\Entity\Appointment;
use Fsb\UserBundle\Entity\User;
use Fsb\UserBundle\Entity\UserRole;
use Fsb\AppointmentBundle\Entity\AppointmentDetail;
use Fsb\AppointmentBundle\Test\Entity\AppointmentDefaultEntityTest;

class AppointmentDetailTest extends AppointmentDefaultEntityTest
{
	private $recruiter;
	private $appointmentSetter;
	private $appointmentDetail;
	
	
	public function setUp()
	{
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
		
		$this->globalValidation($appointment);
		
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
		
		$startDate = new \DateTime('today');
		$appointment->setStartDate($startDate);
		$this->assertEquals(
				$startDate,
				$appointment->getStartDate(),
				'The start date is saved in the appointment'
		);
		
		$endDate = new \DateTime('tomorrow');
		$appointment->setEndDate($endDate);
		$this->assertEquals(
				$endDate,
				$appointment->getEndDate(),
				'The end date is saved in the appointment'
		);
		
		$appointment->setOrigin('origin');
		$this->assertEquals(
				'origin',
				$appointment->getOrigin(),
				'The origin is saved in the appointment'
		);
		
		$appointment->setFileName('fileName');
		$this->assertEquals(
				'fileName',
				$appointment->getFileName(),
				'The file Name is saved in the appointment'
		);
		
		$appointment->setAppointmentDetail($appointmentDetail);
		$this->assertEquals(
				'title',
				$appointment->__toString(),
				'The appointmentDetail setted in the appointment is the same than the appointmentDetail getted from the appointment stored in the apppointmentDetail'
		);
	}
}