<?php

namespace Fsb\RuleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * AvailabilityFilter
 *
 */
class AvailabilityFilter
{
    /**
     *
     */
    private $recruiters;
    
    /**
     *
     * @var Time
     *
     */
    private $startTime;
    
    /**
     *
     * @var Time
     *
     */
    private $endTime;
    
    /**
   
    
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
     * @param ArrayCollection $projects $recruiters
     * 
     * @return \Fsb\RuleBundle\Entity\AvailabilityFilter
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
     * Set startTime
     *
     * @param \Time $startTime
     * @return \Fsb\RuleBundle\Entity\AvailabilityFilter
     */
    public function setStartTime($startTime)
    {
    	$this->startTime = $startTime;
    
    	return $this;
    }
    
    /**
     * Get startTime
     *
     * @return \Time
     */
    public function getStartTime()
    {
    	return $this->startTime;
    }
    
    /**
     * Set endTime
     *
     * @param \Time endTime
     * @return \Fsb\RuleBundle\Entity\AvailabilityFilter
     */
    public function setEndTime($endTime)
    {
    	$this->endTime = $endTime;
    
    	return $this;
    }
    
    /**
     * Get endTime
     *
     * @return \Time
     */
    public function getEndTime()
    {
    	return $this->endTime;
    }
    
}
