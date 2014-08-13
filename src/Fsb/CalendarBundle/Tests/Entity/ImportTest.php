<?php
namespace Fsb\CalendarBundle\Tests\Entity;

use Symfony\Component\Validator\Validation;
use Fsb\CalendarBundle\Entity\Import;
use Fsb\UserBundle\Entity\User;
use Fsb\AppointmentBundle\Entity\AppointmentProject;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImportTest extends \PHPUnit_Framework_TestCase
{
	private $validator;
	private $recruiter;
	private $project;
	private $file;

	public function setUp()
	{
		$this->validator = Validation::createValidatorBuilder()
		->enableAnnotationMapping()
		->getValidator();

		$recruiter = new User();
		$recruiter->setLogin('login');
		$this->recruiter = $recruiter;
		
		$project =  new AppointmentProject();
		$project->setName('project');
		$this->project = $project;
		
	}

	public function testValidation() {

		$import = new Import();

		$import->setRecruiter($this->recruiter);
		$this->assertEquals(
				'login',
				$import->getRecruiter()->getLogin(),
				'The recruiter is saved in the import entity'
		);
		
		$import->setProject($this->project);
		$this->assertEquals(
				'project',
				$import->getProject()->getName(),
				'The recruiter is saved in the import entity'
		);
		
		$import->setFilePath('filePath');
		$this->assertEquals(
				'filePath',
				$import->getFilePath(),
				'The filePath is saved in the import entity'
		);
	}

}