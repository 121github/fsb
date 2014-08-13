<?php
namespace Fsb\UserBundle\Tests\Entity;

use Symfony\Component\Validator\Validation;
use Fsb\UserBundle\Entity\UserChangePassword;

class UserChangePasswordTest extends \PHPUnit_Framework_TestCase
{

	private $validator;
	
	public function setUp()
	{
		$this->validator = Validation::createValidatorBuilder()
		->enableAnnotationMapping()
		->getValidator();
	}

	public function testValidation() {
		$userChangePassword = new UserChangePassword();
		
		$userChangePassword->setOldPassword('oldPassword');
		$this->assertEquals(
				'oldPassword',
				$userChangePassword->getOldPassword(),
				'The oldPassowrd are saved in the UserChangePassword'
		);
		
		$userChangePassword->setPassword('password');
		$this->assertEquals(
				'password',
				$userChangePassword->getPassword(),
				'The Passowrd are saved in the UserChangePassword'
		);
	}
	
}

