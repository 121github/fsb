<?php
namespace Fsb\ReportingBundle\Tests\Entity;

use Symfony\Component\Validator\Validation;
use Fsb\AppointmentBundle\Entity\AppointmentFilter;
use Fsb\ReportingBundle\Entity\ReportingFilter;

class ReportingFilterTest extends \PHPUnit_Framework_TestCase
{
	private $validator;

	public function setUp()
	{
		$this->validator = Validation::createValidatorBuilder()
		->enableAnnotationMapping()
		->getValidator();

	}

	public function testValidation() {

		$reportingFilter = new ReportingFilter();
		
		$reportingFilter->setReports('byMonth');
		$this->assertEquals(
				'byMonth',
				$reportingFilter->getReports(),
				'The reports is saved in the reporting filter entity'
		);
		
		$reportingFilter->setReports('byRecruiter');
		$this->assertEquals(
				'byRecruiter',
				$reportingFilter->__toString(),
				'The toString method returns the reports saved in the reporting filter entity'
		);
	}
	
}