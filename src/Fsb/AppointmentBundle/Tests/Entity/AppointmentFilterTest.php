<?php
namespace Fsb\AppointmentBundle\Tests\Entity;

use Symfony\Component\Validator\Validation;
use Fsb\AppointmentBundle\Entity\AppointmentFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Fsb\UserBundle\Entity\User;
use Fsb\AppointmentBundle\Entity\AppointmentProject;
use Fsb\AppointmentBundle\Entity\AppointmentOutcome;

class AppointmentFilterTest extends \PHPUnit_Framework_TestCase
{
	
	private $validator;
	private $recruiters;
	private $projects;
	private $outcomes;
	
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
		
		$appointmentFilter = new AppointmentFilter();
		
		$appointmentFilter->setRecruiters($this->recruiters);
		$this->assertGreaterThan(
				0,
				$appointmentFilter->getRecruiters()->count(),
				'The recruiters are saved in the appointment Filter'
		);
		
		$appointmentFilter->setProjects($this->projects);
		$this->assertGreaterThan(
				0,
				$appointmentFilter->getProjects()->count(),
				'The projecrts are saved in the appointment Filter'
		);
		
		$appointmentFilter->setOutcomes($this->outcomes);
		$this->assertGreaterThan(
				0,
				$appointmentFilter->getOutcomes()->count(),
				'The outcomes are saved in the appointment Filter'
		);
		
		$appointmentFilter->setPostcode('postcode');
		$this->assertEquals(
				'postcode',
				$appointmentFilter->getPostcode(),
				'The postcode is saved in the appointment Filter'
		);
		
		$appointmentFilter->setRange(50);
		$this->assertEquals(
				50,
				$appointmentFilter->getRange(),
				'The range is saved in the appointment Filter'
		);
	}
}