<?php
namespace Fsb\UserBundle\Tests\Entity;

use Symfony\Component\Validator\Validation;
use Fsb\UserBundle\Tests\Entity\UserDefaultEntityTest;
use Fsb\UserBundle\Entity\UserDetail;

class UserDetailTest extends UserDefaultEntityTest
{
	
	public function setUp()
	{

	}

	public function testValidation()
	{
		$userDetail = new UserDetail();

		$this->globalValidation($userDetail);

		$userDetail->setFirstname('firstname');
		$this->assertEquals(
				'firstname',
				$userDetail->getFirstname(),
				'The firstname is saved in the userDetail'
		);
		
		$userDetail->setLastname('lastname');
		$this->assertEquals(
				'lastname',
				$userDetail->getLastname(),
				'The lastname is saved in the userDetail'
		);

		$userDetail->setEmail('email');
		$this->assertEquals(
				'email',
				$userDetail->getEmail(),
				'The email is saved in the userDetail'
		);
		
		$userDetail->setMobile('mobile');
		$this->assertEquals(
				'mobile',
				$userDetail->getMobile(),
				'The mobile is saved in the userDetail'
		);
		
		$userDetail->setTelephone('telephone');
		$this->assertEquals(
				'telephone',
				$userDetail->getTelephone(),
				'The telephone is saved in the userDetail'
		);
	}
}