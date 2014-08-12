<?php

use Symfony\Component\Validator\Validation;
use Fsb\ReportingBundle\Entity\ReportingFilterByMonth;
use Doctrine\Common\Collections\ArrayCollection;
use Fsb\UserBundle\Entity\User;

class ReportingFilterByMonthTest extends \PHPUnit_Framework_TestCase
{
	private $validator;
	private $recruiters;
	private $appointmentSetters;

	public function setUp()
	{
		$this->validator = Validation::createValidatorBuilder()
		->enableAnnotationMapping()
		->getValidator();

		$recruiters = new ArrayCollection();
		$recruiter = new User();
		$recruiter->setLogin('login');
		$recruiters->add($recruiter);
		$this->recruiters = $recruiters;
		
		$appointmentSetters = new ArrayCollection();
		$appointmentSetter = new User();
		$appointmentSetter->setLogin('login');
		$appointmentSetters->add($appointmentSetter);
		$this->appointmentSetters = $appointmentSetters;
	}

	public function testValidation() {

		$reportingFilterByMonth = new ReportingFilterByMonth();

		$reportingFilterByMonth->setRecruiters($this->recruiters);
		$this->assertGreaterThan(
				0,
				$reportingFilterByMonth->getRecruiters()->count(),
				'The recruiters are saved in the reporting Filter by month'
		);
	
		$reportingFilterByMonth->setAppointmentSetters($this->appointmentSetters);
		$this->assertGreaterThan(
				0,
				$reportingFilterByMonth->getAppointmentSetters()->count(),
				'The appointmentSetters are saved in the reporting Filter by month'
		);
		
		$reportingFilterByMonth->setYear('year');
		$this->assertEquals(
				'year',
				$reportingFilterByMonth->getYear(),
				'The year is saved in the reporting Filter by month'
		);
	}

}