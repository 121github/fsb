<?php

namespace Fsb\ReportingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Filter
 *
 */
class ReportingFilter
{
    /**
     *
     */
    private $reports;

	
	public function __construct()
	{
		$this->reports = array();
	}
    
    /**
     *
     * @return string
     */
    public function __toString()
    {
    	return $this->reports;
    }
    
    
    /**
     * Set reports
     *
     * @param $reports
     * 
     * @return \Fsb\ReportingBundle\Entity\ReportingFilter
     */
    public function setReports($reports)
    {
    	$this->reports = $reports;
    
    	return $this;
    }
    
    /**
     * Get reports
     *
     * @return array
     */
    public function getReports()
    {
    	return $this->reports;
    }
}
