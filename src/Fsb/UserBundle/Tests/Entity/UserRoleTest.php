<?php

use Symfony\Component\Validator\Validation;
use Fsb\UserBundle\Test\Entity\UserDefaultEntityTest;
use Fsb\UserBundle\Entity\UserRole;

class UserRoleTest extends UserDefaultEntityTest
{
	public function setUp()
	{
	
	}

	public function testValidation()
	{
		$userRole = new UserRole();

		$this->globalValidation($userRole);

		$userRole->setName('name');
		$this->assertEquals(
				'name',
				$userRole->getName(),
				'The name is saved in the userRole entity'
		);
	}
}
