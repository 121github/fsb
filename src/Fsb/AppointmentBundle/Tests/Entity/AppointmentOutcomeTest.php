<?php

use Symfony\Component\Validator\Validation;
use Fsb\AppointmentBundle\Entity\AppointmentOutcome;

class AppointmentOutcomeTest extends \PHPUnit_Framework_TestCase
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
		$appointmentOutcome = new AppointmentOutcome();
		
		
		$appointmentOutcome->setName('name');
		$this->assertEquals(
				'name',
				$appointmentOutcome->getName(),
				'The name is saved in the appointmentOutcome'
		);
		
	}
	
}