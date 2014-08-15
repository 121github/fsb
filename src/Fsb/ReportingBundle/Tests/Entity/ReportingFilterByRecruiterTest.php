<?php
namespace Fsb\ReportingBundle\Tests\Entity;

use Symfony\Component\Validator\Validation;
use Fsb\UserBundle\Entity\User;
use Fsb\ReportingBundle\Entity\ReportingFilterByRecruiter;

class ReportingFilterByRecruiterTest extends \PHPUnit_Framework_TestCase
{
	private $validator;

	public function setUp()
	{
		$this->validator = Validation::createValidatorBuilder()
		->enableAnnotationMapping()
		->getValidator();
	}

	public function testValidation() {

		$repFilterByRecruiter = new ReportingFilterByRecruiter();

		$startDate = new \DateTime('today');
		$repFilterByRecruiter->setStartDate($startDate);
		$this->assertEquals(
				$startDate,
				$repFilterByRecruiter->getStartDate(),
				'The start Date are saved in the reporting Filter by recruiter'
		);
		
		$endDate = new \DateTime('today');
		$repFilterByRecruiter->setEndDate($endDate);
		$this->assertEquals(
				$endDate,
				$repFilterByRecruiter->getEndDate(),
				'The end Date are saved in the reporting Filter by recruiter'
		);

	}

}