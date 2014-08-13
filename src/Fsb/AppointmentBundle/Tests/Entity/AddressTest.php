<?php
namespace Fsb\AppointmentBundle\Tests\Entity;

use Symfony\Component\Validator\Validation;
use Fsb\AppointmentBundle\Entity\Address;
use Fsb\AppointmentBundle\Test\Entity\AppointmentDefaultEntityTest;

class AddressTest extends \PHPUnit_Framework_TestCase 
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
		$address = new Address();
		
		//$this->globalValidation($address);
		
		$address->setAdd1('add1');
		$this->assertEquals(
				'add1',
				$address->getAdd1(),
				'The add1 is saved in the address'
		);
		
		$address->setAdd2('add2');
		$this->assertEquals(
				'add2',
				$address->getAdd2(),
				'The add2 is saved in the address'
		);
		
		$address->setAdd3('add3');
		$this->assertEquals(
				'add3',
				$address->getAdd3(),
				'The add3 is saved in the address'
		);
		
		$address->setPostcode('postcode');
		$this->assertEquals(
				'postcode',
				$address->getPostcode(),
				'The postcode is saved in the address'
		);
		
		$address->setTown('town');
		$this->assertEquals(
				'town',
				$address->getTown(),
				'The town is saved in the address'
		);
		
		$address->setCountry('country');
		$this->assertEquals(
				'country',
				$address->getCountry(),
				'The country is saved in the address'
		);
		
		$address->setLat('lat');
		$this->assertEquals(
				'lat',
				$address->getLat(),
				'The lat is saved in the address'
		);
		
		$address->setLon('lon');
		$this->assertEquals(
				'lon',
				$address->getLon(),
				'The lon is saved in the address'
		);
		
		$address->setCreatedBy(1);
		$this->assertEquals(
				1,
				$address->getCreatedBy(),
				'The creator user is saved in the entity'
		);
		
		$creationDate = new \DateTime('today');
		$address->setCreatedDate($creationDate);
		$this->assertEquals(
				$creationDate,
				$address->getCreatedDate(),
				'The created date is saved in the entity'
		);
		
		$address->setModifiedBy(1);
		$this->assertEquals(
				1,
				$address->getModifiedBy(),
				'The modifier user is saved in the entity'
		);
		
		$modifiedDate = new \DateTime('tomorrow');
		$address->setModifiedDate($modifiedDate);
		$this->assertEquals(
				$modifiedDate,
				$address->getModifiedDate(),
				'The modified date is saved in the entity'
		);
		
		$addressToString = $address->getAdd1().' '.$address->getAdd2().' '.$address->getAdd3().' '.$address->getPostcode().' '.$address->getTown().' '.$address->getCountry();
		$this->assertEquals(
				$addressToString,
				$address->__toString(),
				'The toString method prints the address saved in the address entity'
		);
		
	}
	
}