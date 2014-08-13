<?php
namespace Fsb\RuleBundle\Tests\Entity;

use Symfony\Component\Validator\Validation;
use Fsb\RuleBundle\Tests\Entity\RuleDefaultEntityTest;
use Fsb\RuleBundle\Entity\Rule;
use Fsb\UserBundle\Entity\User;

class RuleTest extends RuleDefaultEntityTest
{
	private $recruiter;
	
	public function setUp()
	{

		$recruiter = new User();
		$recruiter->setLogin('login');
		$this->recruiter = $recruiter;
		
	}

	public function testValidation()
	{
		$rule = new Rule();
		
		$this->globalValidation($rule);
		
		$rule->setRecruiter($this->recruiter);
		$this->assertEquals(
				'login',
				$rule->getRecruiter()->getLogin(),
				'The recruiter is saved in the rule entity'
		);
		
		$rule->setRule('rule');
		$this->assertEquals(
				'rule',
				$rule->getRule(),
				'The rule is saved in the rule entity'
		);
		
		$rule->setDescription('description');
		$this->assertEquals(
				'description',
				$rule->getDescription(),
				'The description is saved in the rule entity'
		);
		
		$rule->setRule('rule');
		$this->assertEquals(
				'rule',
				$rule->__toString(),
				'The toString method print the rule saved in the rule entity'
		);
	}
	
}