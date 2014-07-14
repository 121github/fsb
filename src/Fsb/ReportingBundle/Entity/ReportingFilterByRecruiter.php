<?php

namespace Fsb\ReportingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Filter
 *
 */
class ReportingFilterByRecruiter
{
   
	/**
	 * @var \DateTime
	 */
	private $startDate;
	
	/**
	 * @var \DateTime
	 */
	private $endDate;
	
	
	/**
     *
     * @return string
     */
    public function __toString()
    {
    	return $this->recruiter;
    }
    
    /**
     * 
     * @return DateTime
     */
	public function getStartDate() {
		return $this->startDate;
	}
	
	/**
	 * 
	 * @param \DateTime $startDate
	 * @return \Fsb\ReportingBundle\Entity\ReportingFilterByRecruiter
	 */
	public function setStartDate(\DateTime $startDate) {
		$this->startDate = $startDate;
		return $this;
	}
	
	/**
	 * 
	 * @return DateTime
	 */
	public function getEndDate() {
		return $this->endDate;
	}
	
	/**
	 * 
	 * @param \DateTime $endDate
	 * @return \Fsb\ReportingBundle\Entity\ReportingFilterByRecruiter
	 */
	public function setEndDate(\DateTime $endDate) {
		$this->endDate = $endDate;
		return $this;
	}
    
}
