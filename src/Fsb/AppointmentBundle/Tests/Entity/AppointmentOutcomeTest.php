<?php
namespace Fsb\AppointmentBundle\Tests\Entity;

use Symfony\Component\Validator\Validation;
use Fsb\AppointmentBundle\Entity\AppointmentOutcome;
use Fsb\AppointmentBundle\Tests\Entity\AppointmentDefaultEntityTest;

class AppointmentOutcomeTest extends AppointmentDefaultEntityTest
{

	public function setUp()
	{

	}

	public function testValidation()
	{
		$appointmentOutcome = new AppointmentOutcome();
		
		$this->globalValidation($appointmentOutcome);
		
		$appointmentOutcome->setName('name');
		$this->assertEquals(
				'name',
				$appointmentOutcome->getName(),
				'The name is saved in the appointmentOutcome'
		);
		
		$appointmentOutcome->setName('name');
		$this->assertEquals(
				'name',
				$appointmentOutcome->__toString(),
				'The toString method prints the name saved in the appointmentOutcome entity'
		);
		
	}
	
}