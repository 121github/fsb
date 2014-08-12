<?php
use Symfony\Component\Validator\Validation;
use Doctrine\Common\Collections\ArrayCollection;
use Fsb\UserBundle\Entity\UserRole;
use Fsb\UserBundle\Entity\UserFilter;

class UserFilterTest extends \PHPUnit_Framework_TestCase
{

	private $validator;
	private $roles;

	public function setUp()
	{
		$this->validator = Validation::createValidatorBuilder()
		->enableAnnotationMapping()
		->getValidator();

		$roles = new ArrayCollection();
		$role = new UserRole();
		$role->setName('role');
		$roles->add($role);
		$this->roles = $roles;
	}

	public function testValidation() {
		$userFilter = new UserFilter();
		
		$userFilter->setRoles($this->roles);
		$this->assertGreaterThan(
				0,
				$userFilter->getRoles()->count(),
				'The roles are saved in the UserFilter'
		);
	}
	
}

