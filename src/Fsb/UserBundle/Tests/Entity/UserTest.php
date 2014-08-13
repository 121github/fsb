<?php
namespace Fsb\UserBundle\Tests\Entity;

use Symfony\Component\Validator\Validation;
use Fsb\UserBundle\Tests\Entity\UserDefaultEntityTest;
use Fsb\UserBundle\Entity\User;
use Fsb\UserBundle\Entity\UserRole;
use Fsb\UserBundle\Entity\UserDetail;

class UserTest extends UserDefaultEntityTest
{
	private $role;
	private $userDetail;
	
	public function setUp()
	{
		
		$role = new UserRole();
		$role->setName('role');
		$this->role = $role;
		
		$userDetail = new UserDetail();
		$userDetail->setFirstname('firstname');
		$userDetail->setLastname('lastname');
		$this->userDetail = $userDetail;
	}

	public function testValidation()
	{
		$user = new User();

		$this->globalValidation($user);

		$user->setLogin('login');
		$this->assertEquals(
				'login',
				$user->getLogin(),
				'The login is saved in the user'
		);
		
		$user->setPassword('password');
		$this->assertEquals(
				'password',
				$user->getPassword(),
				'The password is saved in the user'
		);
		
		$user->setSalt('salt');
		$this->assertEquals(
				'salt',
				$user->getSalt(),
				'The salt is saved in the user'
		);
		
		$user->setRole($this->role);
		$this->assertEquals(
				'role',
				$user->getRole()->getName(),
				'The role is saved in the user'
		);
		
		$user->setUserDetail($this->userDetail);
		$this->assertEquals(
				'firstname',
				$user->getUserDetail()->getFirstname(),
				'The userDetail is saved in the user'
		);
		
		$userDetail = $user->getUserDetail();
		$userDetail->setUser($user);
		$this->assertEquals(
				$user->getUserDetail()->getFirstname(),
				$userDetail->getUser()->getUserDetail()->getFirstname(),
				'The userDetail setted in the user is the same than the userDetail getted from the user stored in the userDetail'
		);
		
		$user->setUserDetail($this->userDetail);
		$this->assertEquals(
				'firstname lastname',
				$user->__toString(),
				'The method toString prints the firstname and lastname saved in the userDetail into the user'
		);
	}
}