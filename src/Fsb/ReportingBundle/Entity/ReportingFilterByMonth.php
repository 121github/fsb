<?php

namespace Fsb\ReportingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Filter
 *
 */
class ReportingFilterByMonth
{
    /**
     *
     */
    private $recruiters;

    /**
     *
     */
    private $appointmentSetters;
    
	
	public function __construct()
	{
		$this->recruiters = new ArrayCollection();
		$this->appointmentSetters = new ArrayCollection();
	}
    
    /**
     *
     * @return string
     */
    public function __toString()
    {
    	return $this->recruiter;
    }
    
    
    /**
     * Set recruiters
     *
     * @param ArrayCollection $recruiters
     * 
     * @return \Fsb\ReportingBundle\Entity\ReportingFilterByMonth
     */
    public function setRecruiters(ArrayCollection $recruiters)
    {
    	$this->recruiters = $recruiters;
    
    	return $this;
    }
    
    /**
     * Get recruiters
     *
     * @return ArrayCollection
     */
    public function getRecruiters()
    {
    	return $this->recruiters;
    }
    
    /**
     * Set appointmentSetters
     *
     * @param ArrayCollection $appointmentSetters
     *
     * @return \Fsb\ReportingBundle\Entity\ReportingFilterByMonth
     */
    public function setAppointmentSetters(ArrayCollection $appointmentSetters)
    {
    	$this->appointmentSetters = $appointmentSetters;
    
    	return $this;
    }
    
    /**
     * Get appointmentSetters
     *
     * @return ArrayCollection
     */
    public function getAppointmentSetters()
    {
    	return $this->appointmentSetters;
    }
    
}
