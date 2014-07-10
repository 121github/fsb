<?php

namespace Fsb\AppointmentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Filter
 *
 */
class AppointmentFilter
{
    /**
     *
     */
    private $recruiters;

    /**
     *
     */
    private $projects;
    
    /**
     * 
     */
	private $outcomes;
	
	/**
	 * 
	 */
	private $postcode;
	
	/**
	 * 
	 */
	private $range;
	
    
	public function __construct()
	{
		$this->recruiters = new ArrayCollection();
		$this->outcomes = new ArrayCollection();
		$this->projects = new ArrayCollection();
	}
    
    /**
     *
     * @return string
     */
    public function __toString()
    {
    	return $this->recruiters;
    }
    
    
    /**
     * Set recruiters
     *
     * @param ArrayCollection $recruiters
     * 
     * @return \Fsb\CalendarBundle\Entity\Filter
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
     * Set projects
     *
     * @param ArrayCollection $projects
     * 
     * @return \Fsb\CalendarBundle\Entity\Filter
     */
    public function setProjects(ArrayCollection $projects)
    {
    	$this->projects = $projects;
    
    	return $this;
    }
    
    /**
     * Get projects
     *
     * @return ArrayCollection
     */
    public function getProjects()
    {
    	return $this->projects;
    }
    

    /**
     * Set Outcomes
     *
     * @param ArrayCollection $outcomes
     * 
     * @return \Fsb\CalendarBundle\Entity\Filter
     *
     */
    public function setOutcomes(ArrayCollection $outcomes)
    {
    	$this->outcomes = $outcomes;
    
    	return $this;
    }
    
    /**
     * Get outcomes
     *
     * @return ArrayCollection
     */
    public function getOutcomes()
    {
    	return $this->outcomes;
    }
    
    
    /**
     * Get Postcode
     * 
     * @return String
     */
	public function getPostcode() {
		return $this->postcode;
	}
	
	/**
	 * 
	 * @param unknown $postcode
	 * 
	 * 
	 * @return \Fsb\CalendarBundle\Entity\Filter
	 */
	public function setPostcode($postcode) {
		$this->postcode = $postcode;
		return $this;
	}
	
	/**
	 * Get Range (miles)
	 * 
	 * @return double
	 */
	public function getRange() {
		return $this->range;
	}
	
	/**
	 * 
	 * @param unknown $range
	 * 
	 * 
	 * @return \Fsb\CalendarBundle\Entity\Filter
	 */
	public function setRange($range) {
		$this->range = $range;
		return $this;
	}
	
    
    
    
}
