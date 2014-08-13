<?php
namespace Fsb\AppointmentBundle\Tests\Entity;

use Symfony\Component\Validator\Validation;

class AppointmentDefaultEntityTest extends \PHPUnit_Framework_TestCase
{
	protected $validator;
	
	protected function setUp()
	{
		$this->validator = Validation::createValidatorBuilder()
		->enableAnnotationMapping()
		->getValidator();
	}
	
	public function testValidation() {
		
	}
	
	protected function globalValidation($entity) {
		$entity->setCreatedBy(1);
		$this->assertEquals(
				1,
				$entity->getCreatedBy(),
				'The creator user is saved in the entity'
		);
		
		$creationDate = new \DateTime('today');
		$entity->setCreatedDate($creationDate);
		$this->assertEquals(
				$creationDate,
				$entity->getCreatedDate(),
				'The created date is saved in the entity'
		);
		
		$entity->setModifiedBy(1);
		$this->assertEquals(
				1,
				$entity->getModifiedBy(),
				'The modifier user is saved in the entity'
		);
		
		$modifiedDate = new \DateTime('tomorrow');
		$entity->setModifiedDate($modifiedDate);
		$this->assertEquals(
				$modifiedDate,
				$entity->getModifiedDate(),
				'The modified date is saved in the entity'
		);
	}
}

	
