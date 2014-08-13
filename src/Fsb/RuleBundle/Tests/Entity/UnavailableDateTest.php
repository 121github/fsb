<?php
namespace Fsb\RuleBundle\Tests\Entity;

use Symfony\Component\Validator\Validation;
use Fsb\RuleBundle\Tests\Entity\RuleDefaultEntityTest;
use Fsb\UserBundle\Entity\User;
use Fsb\RuleBundle\Entity\UnavailableDate;
use Fsb\RuleBundle\Entity\UnavailableDateReason;

class UnavailableDateTest extends RuleDefaultEntityTest
{
	private $recruiter;
	private $reason;

	public function setUp()
	{

		$recruiter = new User();
		$recruiter->setLogin('login');
		$this->recruiter = $recruiter;
		
		$reason = new UnavailableDateReason();
		$reason->setReason('reason');
		$this->reason = $reason;
		
	}

	public function testValidation()
	{
		$unavailableDate = new UnavailableDate();

		$this->globalValidation($unavailableDate);

		$unavailableDate->setRecruiter($this->recruiter);
		$this->assertEquals(
				'login',
				$unavailableDate->getRecruiter()->getLogin(),
				'The recruiter is saved in the unavailableDate entity'
		);
		
		$unavailableDate->setReason($this->reason);
		$this->assertEquals(
				'reason',
				$unavailableDate->getReason()->getReason(),
				'The reason is saved in the unavailableDate entity'
		);
		
		$unavailableDate->setOtherReason('otherReason');
		$this->assertEquals(
				'otherReason',
				$unavailableDate->getOtherReason(),
				'The other reason is saved in the unavailableDate entity'
		);
		
		$startDate = new \DateTime('now');
		$endDate = new \DateTime('now + 2 hour');
		
		$unavailableDate->setUnavailableDate($startDate->format('Y-m-d'));
		$this->assertEquals(
				$startDate->format('Y-m-d'),
				$unavailableDate->getUnavailableDate(),
				'The unavailable Date is saved in the unavailableDate entity'
		);
		
// 		$unavailableDate->setUnavailableDate($startDate->format('Y-m-d'));
// 		$this->assertEquals(
// 				$startDate->format('Y-m-d'),
// 				$unavailableDate->__toString(),
// 				'The toString method prints the unavailableDate saved in the unavailableDate entity'
// 		);
		
		$unavailableDate->setStartTime($startDate->format('H:i:s'));
		$this->assertEquals(
				$startDate->format('H:i:s'),
				$unavailableDate->getStartTime(),
				'The startTime is saved in the unavailableDate entity'
		);
		
		$unavailableDate->setEndTime($endDate->format('H:i:s'));
		$this->assertEquals(
				$endDate->format('H:i:s'),
				$unavailableDate->getEndTime(),
				'The EndTime is saved in the unavailableDate entity'
		);
		
		$unavailableDate->setAllDay(true);
		$this->assertEquals(
				1,
				$unavailableDate->getAllDay(),
				'The allDay is saved in the unavailableDate entity'
		);
	}

}