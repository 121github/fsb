<?php
namespace Fsb\AppointmentBundle\Tests\Entity;

use Symfony\Component\Validator\Validation;
use Fsb\AppointmentBundle\Entity\AppointmentRest;
use Fsb\UserBundle\Entity\User;
use Fsb\AppointmentBundle\Entity\AppointmentProject;

class AppointmentRestTest extends \PHPUnit_Framework_TestCase
{

	private $validator;
	private $recruiter;
	private $appointmentSetter;
	private $project;
	
	public function setUp()
	{
		$this->validator = Validation::createValidatorBuilder()
		->enableAnnotationMapping()
		->getValidator();
		
		$recruiter = new User();
		$recruiter->setLogin('login');
		$this->recruiter = $recruiter;
		
		$appointmentSetter = new User();
		$appointmentSetter->setLogin('login');
		$this->appointmentSetter = $appointmentSetter;
		
		$project = new AppointmentProject();
		$project->setName('project');
		$this->project = $project;
	}

	public function testValidation()
	{
		$appointmentRest = new AppointmentRest();
		
		$startDate = new \DateTime('today');
		$appointmentRest->setStartDate($startDate);
		$this->assertEquals(
				$startDate,
				$appointmentRest->getStartDate(),
				'The start Date is saved in the appointment Rest'
		);
		
		$endDate = new \DateTime('today');
		$appointmentRest->setEndDate($endDate);
		$this->assertEquals(
				$endDate,
				$appointmentRest->getEndDate(),
				'The end Date is saved in the appointment Rest'
		);
		
		$appointmentRest->setRecruiter($this->recruiter);
		$this->assertEquals(
				'login',
				$appointmentRest->getRecruiter()->getLogin(),
				'The recruiter is saved in the appointment Rest'
		);
		
		$appointmentRest->setAppointmentSetter($this->appointmentSetter);
		$this->assertEquals(
				'login',
				$appointmentRest->getAppointmentSetter()->getLogin(),
				'The appointmentSetter is saved in the appointment Rest'
		);
		
		
		$appointmentRest->setTitle('title');
		$this->assertEquals(
				'title',
				$appointmentRest->getTitle(),
				'The title is saved in the appointmentRest'
		);
		
		$appointmentRest->setTitle('title');
		$this->assertEquals(
				'title',
				$appointmentRest->__toString(),
				'The title is saved in the appointmentRest'
		);
		
		$appointmentRest->setComment('comment');
		$this->assertEquals(
				'comment',
				$appointmentRest->getComment(),
				'The comment is saved in the appointmentRest'
		);
		
		$appointmentRest->setProject($this->project);
		$this->assertEquals(
				'project',
				$appointmentRest->getProject()->getName(),
				'The project is saved in the appointmentRest'
		);
		
		$appointmentRest->setRecordRef('recordRef');
		$this->assertEquals(
				'recordRef',
				$appointmentRest->getRecordRef(),
				'The record Reference is saved in the appointmentRest'
		);
		
		$appointmentRest->setAdd1('add1');
		$this->assertEquals(
				'add1',
				$appointmentRest->getAdd1(),
				'The add1 is saved in the appointmentRest'
		);
		
		$appointmentRest->setAdd2('add2');
		$this->assertEquals(
				'add2',
				$appointmentRest->getAdd2(),
				'The add2 is saved in the appointmentRest'
		);
		
		$appointmentRest->setAdd3('add3');
		$this->assertEquals(
				'add3',
				$appointmentRest->getAdd3(),
				'The add3 is saved in the appointmentRest'
		);
		
		$appointmentRest->setPostcode('postcode');
		$this->assertEquals(
				'postcode',
				$appointmentRest->getPostcode(),
				'The postcode is saved in the appointmentRest'
		);
		
		$appointmentRest->setTown('town');
		$this->assertEquals(
				'town',
				$appointmentRest->getTown(),
				'The town is saved in the appointmentRest'
		);
		
		$appointmentRest->setCountry('country');
		$this->assertEquals(
				'country',
				$appointmentRest->getCountry(),
				'The country is saved in the appointmentRest'
		);
	}
	
}