<?php

use Symfony\Component\Validator\Validation;
use Fsb\RuleBundle\Test\Entity\RuleDefaultEntityTest;
use Fsb\RuleBundle\Entity\UnavailableDateReason;

class UnavailableDateReasonTest extends RuleDefaultEntityTest
{
	public function setUp()
	{

	}

	public function testValidation()
	{
		$unavailableDateReason = new UnavailableDateReason();

		$this->globalValidation($unavailableDateReason);

		$unavailableDateReason->setReason('reason');
		$this->assertEquals(
				'reason',
				$unavailableDateReason->getReason(),
				'The reason is saved in the unavailableDateReason entity'
		);
		
		$unavailableDateReason->setReason('reason');
		$this->assertEquals(
				'reason',
				$unavailableDateReason->__toString(),
				'The toString method prints the other resason saved in the unavailableDateReason entity'
		);
	}

}