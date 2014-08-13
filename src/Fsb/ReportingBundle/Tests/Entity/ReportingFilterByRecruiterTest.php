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

		$reportingFilterByRecruiter = new ReportingFilterByRecruiter();

		$startDate = new \DateTime('today');
		$reportingFilterByRecruiter->setStartDate($startDate);
		$this->assertEquals(
				$startDate,
				$reportingFilterByRecruiter->getStartDate(),
				'The start Date are saved in the reporting Filter by recruiter'
		);
		
		$endDate = new \DateTime('today');
		$reportingFilterByRecruiter->setEndDate($endDate);
		$this->assertEquals(
				$endDate,
				$reportingFilterByRecruiter->getEndDate(),
				'The end Date are saved in the reporting Filter by recruiter'
		);

	}

}