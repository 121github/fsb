<?php
use Symfony\Component\Validator\Validation;
use Fsb\BackendBundle\Entity\CompanyProfile;

class CompanyProfileTest extends \PHPUnit_Framework_TestCase
{
	private $validator;

	public function setUp()
	{
		$this->validator = Validation::createValidatorBuilder()
		->enableAnnotationMapping()
		->getValidator();
	}

	public function testValidation() {

		$companyProfile = new CompanyProfile();
		
		$companyProfile->setCode('code');
		$this->assertEquals(
				'code',
				$companyProfile->getCode(),
				'The code is saved in the companyProfile'
		);
		
		$companyProfile->setSalt('salt');
		$this->assertEquals(
				'salt',
				$companyProfile->getSalt(),
				'The salt is saved in the companyProfile'
		);
		
		$companyProfile->setConame('coname');
		$this->assertEquals(
				'coname',
				$companyProfile->getConame(),
				'The coname is saved in the companyProfile'
		);
		
		$companyProfile->setConame('coname');
		$this->assertEquals(
				'coname',
				$companyProfile->__toString(),
				'The coname is saved in the companyProfile'
		);
		
		$companyProfile->setCreatedBy(1);
		$this->assertEquals(
				1,
				$companyProfile->getCreatedBy(),
				'The creator user is saved in the entity'
		);
		
		$creationDate = new \DateTime('today');
		$companyProfile->setCreatedDate($creationDate);
		$this->assertEquals(
				$creationDate,
				$companyProfile->getCreatedDate(),
				'The created date is saved in the entity'
		);
		
		$companyProfile->setModifiedBy(1);
		$this->assertEquals(
				1,
				$companyProfile->getModifiedBy(),
				'The modifier user is saved in the entity'
		);
		
		$modifiedDate = new \DateTime('tomorrow');
		$companyProfile->setModifiedDate($modifiedDate);
		$this->assertEquals(
				$modifiedDate,
				$companyProfile->getModifiedDate(),
				'The modified date is saved in the entity'
		);
	}

}