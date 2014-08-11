<?php

use Symfony\Component\Validator\Validation;
use Fsb\AppointmentBundle\Entity\AppointmentProject;

class AppointmentProjectTest extends \PHPUnit_Framework_TestCase
{
	private $validator;

	public function setUp()
	{
		$this->validator = Validation::createValidatorBuilder()
		->enableAnnotationMapping()
		->getValidator();

		

	}

	public function testValidation()
	{
		$appointmentProject = new AppointmentProject();
		
		
		$appointmentProject->setName('name');
		$this->assertEquals(
				'name',
				$appointmentProject->getName(),
				'The name is saved in the appointmentProject'
		);
		
	}
	
}