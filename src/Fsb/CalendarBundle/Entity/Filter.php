<?php

namespace Fsb\CalendarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Filter
 *
 */
class Filter
{
    /**
     *
     */
    private $recruiter;

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
		$this->outcomes = new ArrayCollection();
		$this->projects = new ArrayCollection();
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
     * Set recruiter
     *
     * @param \Fsb\UserBundle\Entity\User $recruiter
     * 
     * @return \Fsb\CalendarBundle\Entity\Filter
     */
    public function setRecruiter(\Fsb\UserBundle\Entity\User $recruiter = null)
    {
    	$this->recruiter = $recruiter;
    
    	return $this;
    }
    
    /**
     * Get recruiter
     *
     * @return \Fsb\UserBundle\Entity\User
     */
    public function getRecruiter()
    {
    	return $this->recruiter;
    }
    
    /**
     * Set recruiter
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
     * Get project
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
