<?php
use Symfony\Component\Validator\Validation;
use Doctrine\Common\Collections\ArrayCollection;
use Fsb\RuleBundle\Entity\AvailabilityFilter;
use Fsb\UserBundle\Entity\User;

class AvailabilityFilterTest extends \PHPUnit_Framework_TestCase
{

	private $validator;
	private $recruiters;

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
	}

	public function testValidation() {
		$availabilityFilter = new AvailabilityFilter();

		$availabilityFilter->setRecruiters($this->recruiters);
		$this->assertGreaterThan(
				0,
				$availabilityFilter->getRecruiters()->count(),
				'The recruiters are saved in the availability Filter entity'
		);
		
		$startDate = new DateTime('now');
		$endDate = new DateTime('now + 2 hour');
		
		$availabilityFilter->setStartTime($startDate->format('H:i:s'));
		$this->assertEquals(
				$startDate->format('H:i:s'),
				$availabilityFilter->getStartTime(),
				'The startTime is saved in the availability filter entity'
		);
		
		$availabilityFilter->setEndTime($endDate->format('H:i:s'));
		$this->assertEquals(
				$endDate->format('H:i:s'),
				$availabilityFilter->getEndTime(),
				'The EndTime is saved in the availability fitler entity'
		);
	}

}