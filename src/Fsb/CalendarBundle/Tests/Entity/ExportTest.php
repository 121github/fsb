<?php
namespace Fsb\CalendarBundle\Tests\Entity;

use Symfony\Component\Validator\Validation;
use Fsb\CalendarBundle\Entity\Export;
use Fsb\AppointmentBundle\Entity\AppointmentFilter;

class ExportTest extends \PHPUnit_Framework_TestCase
{
	private $validator;
	private $filter;

	public function setUp()
	{
		$this->validator = Validation::createValidatorBuilder()
		->enableAnnotationMapping()
		->getValidator();
		
		$filter = new AppointmentFilter();
		$filter->setPostcode('postcode');
		$this->filter = $filter;
	}

	public function testValidation() {

		$export = new Export();
		
		$export->setDateRangeType('today');
		$this->assertEquals(
				'today',
				$export->getDateRangeType(),
				'The date range typeis saved in the export entity'
		);
		
		$startDate = new \DateTime('today');
		$export->setStartDate($startDate);
		$this->assertEquals(
				$startDate,
				$export->getStartDate(),
				'The start date is saved in the export entity'
		);
		
		$endDate = new \DateTime('tomorrow');
		$export->setEndDate($endDate);
		$this->assertEquals(
				$endDate,
				$export->getEndDate(),
				'The end date is saved in the export entity'
		);
		
		$export->setExportType('type');
		$this->assertEquals(
				'type',
				$export->getExportType(),
				'The export type is saved in the export entity'
		);
		
		$export->setFilter($this->filter);
		$this->assertEquals(
				'postcode',
				$export->getFilter()->getPostcode(),
				'The filter is saved in the export entity'
		);
	}
	
}