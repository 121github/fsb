<?php
use Symfony\Component\Validator\Validation;
use Doctrine\Common\Collections\ArrayCollection;
use Fsb\UserBundle\Entity\User;
use Fsb\AppointmentBundle\Entity\AppointmentProject;
use Fsb\AppointmentBundle\Entity\AppointmentOutcome;
use Fsb\CalendarBundle\Entity\Filter;

class FilterTest extends \PHPUnit_Framework_TestCase
{

	private $validator;
	private $recruiter;
	private $projects;
	private $outcomes;

	public function setUp()
	{
		$this->validator = Validation::createValidatorBuilder()
		->enableAnnotationMapping()
		->getValidator();

		$recruiter = new User();
		$recruiter->setLogin('login');
		$this->recruiter = $recruiter;

		$projects = new ArrayCollection();
		$project = new AppointmentProject();
		$project->setName('project');
		$projects->add($project);
		$this->projects = $projects;

		$outcomes = new ArrayCollection();
		$outcome = new AppointmentOutcome();
		$outcome->setName('outcome');
		$outcomes->add($outcome);
		$this->outcomes = $outcomes;
	}

	public function testValidation() {
		$filter = new Filter();
		
		$filter->setRecruiter($this->recruiter);
		$this->assertEquals(
				'login',
				$filter->getRecruiter()->getLogin(),
				'The recruiter is saved in the Filter'
		);
		
		$filter->setProjects($this->projects);
		$this->assertGreaterThan(
				0,
				$filter->getProjects()->count(),
				'The projecrts are saved in the Filter'
		);
		
		$filter->setOutcomes($this->outcomes);
		$this->assertGreaterThan(
				0,
				$filter->getOutcomes()->count(),
				'The outcomes are saved in the Filter'
		);
		
		$filter->setPostcode('postcode');
		$this->assertEquals(
				'postcode',
				$filter->getPostcode(),
				'The postcode is saved in the Filter'
		);
		
		$filter->setRange(50);
		$this->assertEquals(
				50,
				$filter->getRange(),
				'The range is saved in the Filter'
		);
	}
	
}