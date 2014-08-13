<?php
namespace Fsb\NoteBundle\Tests\Entity;

use Symfony\Component\Validator\Validation;
use Fsb\NoteBundle\Entity\Note;
use Fsb\UserBundle\Entity\User;

class NoteTest extends \PHPUnit_Framework_TestCase
{
	private $validator;
	private $recruiter;

	public function setUp()
	{
		$this->validator = Validation::createValidatorBuilder()
		->enableAnnotationMapping()
		->getValidator();
		
		$recruiter = new User();
		$recruiter->setLogin('login');
		$this->recruiter = $recruiter;
	}

	public function testValidation() {

		$note = new Note();
		
		$note->setRecruiter($this->recruiter);
		$this->assertEquals(
				'login',
				$note->getRecruiter()->getLogin(),
				'The recruiter is saved in the note entity'
		);
		
		$note->setTitle('title');
		$this->assertEquals(
				'title',
				$note->getTitle(),
				'The title is saved in the note entity'
		);
		
		$note->setTitle('title');
		$this->assertEquals(
				'title',
				$note->__toString(),
				'The title is saved in the note entity'
		);
		
		$note->setText('text');
		$this->assertEquals(
				'text',
				$note->getText(),
				'The text is saved in the note entity'
		);
		
		$startDate = new \DateTime('today');
		$note->setStartDate($startDate);
		$this->assertEquals(
				$startDate,
				$note->getStartDate(),
				'The start Date is saved in the note entity'
		);
		
		$endDate = new \DateTime('tomorrow');
		$note->setEndDate($endDate);
		$this->assertEquals(
				$endDate,
				$note->getEndDate(),
				'The end Date is saved in the note entity'
		);
		
		$note->setCreatedBy(1);
		$this->assertEquals(
				1,
				$note->getCreatedBy(),
				'The creator user is saved in the entity'
		);
		
		$creationDate = new \DateTime('today');
		$note->setCreatedDate($creationDate);
		$this->assertEquals(
				$creationDate,
				$note->getCreatedDate(),
				'The created date is saved in the entity'
		);
		
		$note->setModifiedBy(1);
		$this->assertEquals(
				1,
				$note->getModifiedBy(),
				'The modifier user is saved in the entity'
		);
		
		$modifiedDate = new \DateTime('tomorrow');
		$note->setModifiedDate($modifiedDate);
		$this->assertEquals(
				$modifiedDate,
				$note->getModifiedDate(),
				'The modified date is saved in the entity'
		);
	}

}