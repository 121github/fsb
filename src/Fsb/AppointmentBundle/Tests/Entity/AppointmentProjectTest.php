<?php
namespace Fsb\AppointmentBundle\Tests\Entity;

use Symfony\Component\Validator\Validation;
use Fsb\AppointmentBundle\Entity\AppointmentProject;
use Fsb\AppointmentBundle\Tests\Entity\AppointmentDefaultEntityTest;

class AppointmentProjectTest extends AppointmentDefaultEntityTest
{

	public function setUp()
	{

	}

	public function testValidation()
	{
		$appointmentProject = new AppointmentProject();
		
		$this->globalValidation($appointmentProject);
		
		$appointmentProject->setName('name');
		$this->assertEquals(
				'name',
				$appointmentProject->getName(),
				'The name is saved in the appointmentProject'
		);
		
		$appointmentProject->setName('name');
		$this->assertEquals(
				'name',
				$appointmentProject->__toString(),
				'The toString method prints the name saved in the appointmentProject entity'
		);
		
	}
	
}